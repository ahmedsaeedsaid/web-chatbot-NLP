import os
from dotenv import load_dotenv, find_dotenv

# Load .env File.

load_dotenv(find_dotenv())
foundStart = False
for key in os.environ.keys():
    if key == "STARTVAR":
        foundStart = True
        continue
    if  foundStart:
        globals()[key] = os.getenv(key)

