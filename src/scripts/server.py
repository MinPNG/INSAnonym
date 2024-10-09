import pika
import logging
import subprocess
import psutil
import time
import atexit

# Maximum memory available to execute a single script (in Mb)
# e.g 25Gb is 25_000 * 1024 * 1024
THRESHOLD = 30_000 * 1024 * 1024 

# Security RAM offset to have (in Mb)
# e.g 10Gb is 10_000 * 1024 * 1024
RAM_OFFSET = 10_000 * 1024 * 1024

RETRY_SECONDS = 5

children = [] 
def callback(ch, method, properties, body):
    """
    If RAM is free for one process spawn, spawn a process and acknowledge the message.
    If not, will put the message back on queue and retry in n seconds
    """
    logging.info(f'[x] {body.decode()} is received')
    killZombies()
    nb_childs = childCount()
    mem = psutil.virtual_memory()
    if mem.available >= THRESHOLD*(nb_childs +1) + RAM_OFFSET :
        p = subprocess.Popen(['python', '-m', 'scripts.on_anonymized_upload', body.decode()])
                             # stderr=subprocess.DEVNULL,
                             # stdout=subprocess.DEVNULL)
        children.append(p)

        ch.basic_ack(delivery_tag = method.delivery_tag)
        logging.info('[x] Acked')
    else:
        ch.basic_nack(delivery_tag = method.delivery_tag)
        logging.info(f'[x] Non acked: no space left on RAM after {nb_childs} subprocesses. Retrying in {RETRY_SECONDS} seconds')
        time.sleep(RETRY_SECONDS)
    killZombies()
    logging.info(f' - Current running processes: {childCount()}')
    logging.info(children)

def childCount():
    return(len([c for c in children if c.poll() is None]))

def killZombies():
    for c in children:
        if c.poll() is not None: 
            c.communicate()
            children.remove(c)

@atexit.register
def cleanup():
    logging.info('[x] Cleaned up before exiting..')
    for p in children: p.kill()
    logging.info(f' - Killed processes: {len(children)}')
    logging.info("[x] Exiting now...")


# If you want to see all messages, change level field with logging.DEBUG.
# For only starting/finished/error messages, logging.INFO.
# For only error and warnings, logging.
logging.basicConfig(
    format= f"PID %(process)d - %(asctime)s - %(levelname)s - %(pathname)s.(%(lineno)d) - %(message)s", 
    filename='logs/python_server.log', 
    level = logging.INFO,
    datefmt='%d/%m/%Y %H:%M:%S'
)
logging.getLogger("pika").propagate = False # Disable propagation for pika

while True:
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters('rabbitmq', 5672, '/', pika.PlainCredentials("python_server", "guest"), retry_delay=5))
        channel = connection.channel()
        channel.basic_consume(queue="python", on_message_callback=callback, auto_ack=False)
        channel.start_consuming()
# Don't recover if connection was closed by broker
    except pika.exceptions.ConnectionClosedByBroker:
        break
    # Don't recover on channel errors
    except pika.exceptions.AMQPChannelError:
        break
    # Recover on all other connection errors
    except pika.exceptions.AMQPConnectionError:
        continue


