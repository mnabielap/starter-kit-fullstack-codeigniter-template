import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

# Load Refresh Token
refresh_token = utils.load_config("refreshToken")

if not refresh_token:
    print("[ERROR] No refresh token found in secrets.json. Run A2.auth_login.py first.")
    sys.exit(1)

payload = {
    "refreshToken": refresh_token
}

# Send Request
response = utils.send_and_print(
    url=f"{utils.BASE_URL}/auth/refresh-tokens",
    method="POST",
    body=payload,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)

# Update secrets with new tokens
data = response.json()
if response.status_code == 200 and data.get("data"):
    tokens = data["data"] # Structure might be direct or nested depending on implementation
    
    # Check structure
    access = tokens.get("access", {}).get("token")
    refresh = tokens.get("refresh", {}).get("token")
    
    if access and refresh:
        utils.save_config("accessToken", access)
        utils.save_config("refreshToken", refresh)
        print("\n[INFO] Tokens refreshed and saved.")