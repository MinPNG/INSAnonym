import sqlite3
from hashlib import sha256
import getpass



if __name__ == '__main__':
    try:
        conn = sqlite3.connect('database/tables.sqlite3')
        password = getpass.getpass("Please enter a new password: ")
        confirmPassword = getpass.getpass("Please confirm password: ")
        if password != confirmPassword:
            raise Exception('Passwords differ')
        cur = conn.cursor()
        cur.execute("SELECT salt FROM user where username='admin' limit 1")
        salt, = cur.fetchone()
        passwordAndHashed = salt+password
        passwordAndHashed = passwordAndHashed.encode()
        hash = sha256(passwordAndHashed)
        cur = conn.cursor()
        cur.execute("UPDATE user set salt='{}', password='{}' WHERE username='{}'".format(salt, hash.hexdigest(), "admin"))
        conn.commit()
        conn.close()
        print("Updated admin Password")
    except Exception as e:
        print('Error in resetting password: {}'.format(e))
