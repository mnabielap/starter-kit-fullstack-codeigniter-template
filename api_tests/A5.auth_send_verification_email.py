import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

# Load Access Token
token = utils.load_config("accessToken")
headers = {"Authorization": f"Bearer {token}"} if token else {}

# Send Request
utils.send_and_print(
    url=f"{utils.BASE_URL}/auth/send-verification-email",
    method="POST",
    headers=headers,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)