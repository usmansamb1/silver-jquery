# SMS Environment Configuration

Add these variables to your `.env` file:

```env
# SMS Configuration - ConnectSaudi API
SMS_PROVIDER=connectsaudi

# ConnectSaudi SMS API Settings
CONNECTSAUDI_SMS_URL=https://sms.connectsaudi.com/sendurl.aspx
CONNECTSAUDI_SMS_USER=your_connectsaudi_username
CONNECTSAUDI_SMS_PASSWORD=your_connectsaudi_password
CONNECTSAUDI_SMS_SENDER_ID="Advance Dig"
CONNECTSAUDI_SMS_COUNTRY_CODE=966
CONNECTSAUDI_SMS_PRIORITY=High

# Backup SMS Provider (Authentica) - Optional
AUTHENTICA_SMS_URL=https://api.authentica.sa/api/v1/send-otp
AUTHENTICA_SMS_TOKEN=your_authentica_token

# SMS Service Settings
SMS_API_TIMEOUT=30
SMS_MAX_RETRIES=3
SMS_RETRY_DELAY=5
SMS_MAX_MESSAGE_LENGTH=160
SMS_ENABLE_LOGGING=true
SMS_ENABLE_FALLBACK=false
```

## Configuration Instructions

1. Replace `your_connectsaudi_username` with your actual ConnectSaudi username
2. Replace `your_connectsaudi_password` with your actual ConnectSaudi password  
3. Update `CONNECTSAUDI_SMS_SENDER_ID` if you want to change the sender ID
4. Set `SMS_ENABLE_FALLBACK=true` if you want to use Authentica as fallback
5. Configure `AUTHENTICA_SMS_TOKEN` if using fallback option

## Testing the Configuration

Run the following command to test your SMS configuration:

```bash
php artisan sms:test --config-only
```

To send a test SMS:

```bash
php artisan sms:test 966501234567
```