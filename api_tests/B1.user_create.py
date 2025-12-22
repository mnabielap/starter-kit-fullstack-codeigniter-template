import sys
import os
import time
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

token = utils.load_config("accessToken")
headers = {"Authorization": f"Bearer {token}"} if token else {}

unique_id = int(time.time())
payload = {
    "name": f"Created via Python {unique_id}",
    "email": f"python_created_{unique_id}@example.com",
    "password": "password123",
    "role": "user"
}

response = utils.send_and_print(
    url=f"{utils.BASE_URL}/users",
    method="POST",
    headers=headers,
    body=payload,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)

# Save the ID of the created user to test Update/Delete/GetOne later
data = response.json()
if response.status_code == 201 and data.get("data"):
    created_id = data["data"]["id"]
    utils.save_config("created_user_id", created_id)
    print(f"\n[INFO] Created User ID {created_id} saved to secrets.json")