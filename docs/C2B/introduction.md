---
title: Mpesa C2B APIs
weight: 1
---

## What is C2B?

C2B (Customer to Business) is a payment service that allows customers to pay for goods and services using their mobile phones. The customer initiates the payment process by sending a payment request to the business. The business then processes the payment and sends a confirmation message to the customer.

This trait contains several methods that will help you get started with the C2B APIs.

The methods include:

- Registering URLs
- Simulating a transaction (STK)
- Querying a transaction status (STK)

**Note**: All the resposes from the Safaricom API are in JSON format. Hence we will be returning a `JsonResponse` in all the methods.

The following .env variables are required in order to authenticate with the Mpesa API:

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENV=
MPESA_SHORTCODE=
MPESA_PASSKEY=
MPESA_INITIATOR_NAME=
MPESA_INITIATOR_PASSWORD=
```
