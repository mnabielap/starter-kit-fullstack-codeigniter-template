import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

token = utils.load_config("accessToken")
headers = {"Authorization": f"Bearer {token}"} if token else {}

target_id = utils.load_config("created_user_id")
if not target_id:
    print("[WARN] No created_user_id found. Defaulting to ID 1 (Admin)")
    target_id = "1"

response = utils.send_and_print(
    url=f"{utils.BASE_URL}/users/{target_id}",
    method="GET",
    headers=headers,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)