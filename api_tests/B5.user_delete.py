import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

token = utils.load_config("accessToken")
headers = {"Authorization": f"Bearer {token}"} if token else {}

target_id = utils.load_config("created_user_id")
if not target_id:
    print("[ERROR] No created_user_id found. Run B1 first.")
    sys.exit(1)

response = utils.send_and_print(
    url=f"{utils.BASE_URL}/users/{target_id}",
    method="DELETE",
    headers=headers,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)