import sys
import os
import time
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

# Prepare Payload
# We use a timestamp to make the email unique so you can run this multiple times
unique_id = int(time.time())
payload = {
    "name": f"Test User {unique_id}",
    "email": f"newuser_{unique_id}@example.com",
    "password": "password123" # Meets requirements (letters + numbers)
}

# Send Request
response = utils.send_and_print(
    url=f"{utils.BASE_URL}/auth/register",
    method="POST",
    body=payload,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)

# We don't save tokens here, we rely on Login (A2) to set the main session