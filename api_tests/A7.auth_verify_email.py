import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

token_param = "INVALID_OR_TEST_TOKEN"

# Send Request
utils.send_and_print(
    url=f"{utils.BASE_URL}/auth/verify-email?token={token_param}",
    method="POST",
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)