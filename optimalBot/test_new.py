import bcrypt
password = b"super secret password"
if bcrypt.checkpw(password, hashed):
    print('matched!')