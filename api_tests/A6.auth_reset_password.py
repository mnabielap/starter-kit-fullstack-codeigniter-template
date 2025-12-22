import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

# Since we can't auto-fetch the email token, we use a placeholder to test the route
# To test success, check your logs/email, get the token, and replace 'TEST_TOKEN' manually (temporarily)
token_param = "INVALID_OR_TEST_TOKEN" 

payload = {
    "password": "newpassword123"
}

# Send Request (Query param token + Body password)
utils.send_and_print(
    url=f"{utils.BASE_URL}/auth/reset-password?token={token_param}",
    method="POST",
    body=payload,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)