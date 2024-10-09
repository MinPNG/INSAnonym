# producer.py
# This script will publish MQ message to my_exchange MQ exchange

import pika
import argparse

parser = argparse.ArgumentParser()
parser.add_argument("anonymized", help="Anonymized Dataframe filename")
args = parser.parse_args()

connection = pika.BlockingConnection(pika.ConnectionParameters('rabbitmq', 5672, '/', pika.PlainCredentials('python_server', 'guest')))
channel = connection.channel()

channel.queue_declare(queue='python', durable = True)
channel.basic_publish(exchange='', routing_key='python', body=args.anonymized)

connection.close()
