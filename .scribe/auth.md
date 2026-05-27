# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {TOKEN_DARI_LOGIN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Ambil token dari respons endpoint <code>POST /api/login</code>, lalu kirim sebagai <code>Bearer TOKEN</code>.
