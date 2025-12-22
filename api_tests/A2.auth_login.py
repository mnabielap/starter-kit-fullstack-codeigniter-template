import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

# Payload (Default Admin Credentials from AdminSeeder)
payload = {
    "email": "admin@example.com",
    "password": "password123"
}

# Send Request
response = utils.send_and_print(
    url=f"{utils.BASE_URL}/auth/login",
    method="POST",
    body=payload,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)

# Save Tokens to secrets.json for other scripts to use
data = response.json()
if response.status_code == 200 and data.get("data"):
    tokens = data["data"]["tokens"]
    utils.save_config("accessToken", tokens["access"]["token"])
    utils.save_config("refreshToken", tokens["refresh"]["token"])
    print("\n[INFO] Tokens saved to secrets.json successfully.")
else:
    print("\n[ERROR] Login failed. Tokens not saved.")