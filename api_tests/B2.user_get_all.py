import sys
import os
sys.path.append(os.path.abspath(os.path.dirname(__file__)))
import utils

token = utils.load_config("accessToken")
headers = {"Authorization": f"Bearer {token}"} if token else {}

# Query Parameters
query = "page=1&limit=5&sortBy=created_at:desc"

response = utils.send_and_print(
    url=f"{utils.BASE_URL}/users?{query}",
    method="GET",
    headers=headers,
    output_file=f"{os.path.splitext(os.path.basename(__file__))[0]}.json"
)