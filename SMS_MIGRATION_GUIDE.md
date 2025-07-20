# SMS API Migration Guide - ConnectSaudi Integration

## Overview

This guide documents the migration from the old Authentica SMS API to the new ConnectSaudi SMS API for the JoilYaseeir project. The implementation includes robust error handling, logging, retry logic, and is optimized specifically for ConnectSaudi SMS service.

## Changes Made

### 1. Configuration Updates (`config/sms.php`)
- **Simplified configuration** for ConnectSaudi SMS API only
- **Direct configuration** without provider abstraction
- **Enhanced settings** for timeout, retries, message limits, and logging

### 2. Enhanced SMS Service (`app/Services/SmsService.php`)
- **Complete rewrite** with modern PHP 8.3+ features
- **ConnectSaudi API integration** optimized for the specific API format
- **Automatic mobile number formatting** for Saudi Arabia (+966)
- **Message truncation** with configurable limits
- **Retry logic** with exponential backoff
- **Comprehensive error handling** and logging
- **Database logging** integration

### 3. Database Logging (`app/Models/SmsLog.php` + Migration)
- **Complete SMS tracking** with status, provider, and response data
- **Retry tracking** and failure analysis
- **Purpose-based categorization** (OTP, notifications, marketing)
- **Statistics and reporting** capabilities
- **UUID primary keys** following project standards

### 4. Admin Interface (`app/Http/Controllers/Admin/SmsController.php`)
- **SMS management dashboard** with real-time statistics
- **Configuration testing** functionality
- **Test SMS sending** with validation
- **Service status monitoring**

### 5. Artisan Commands (`app/Console/Commands/TestSmsService.php`)
- **CLI testing** of SMS configuration
- **Test message sending** from command line
- **Statistics display** and monitoring

## Implementation Steps

### Step 1: Update Environment Variables

Add these variables to your `.env` file:

```env
# ConnectSaudi SMS API Settings
CONNECTSAUDI_SMS_URL=https://sms.connectsaudi.com/sendurl.aspx
CONNECTSAUDI_SMS_USER=your_connectsaudi_username
CONNECTSAUDI_SMS_PASSWORD=your_connectsaudi_password
CONNECTSAUDI_SMS_SENDER_ID="Advance Dig"
CONNECTSAUDI_SMS_COUNTRY_CODE=966
CONNECTSAUDI_SMS_PRIORITY=High

# SMS Service Settings (Optional - defaults provided)
SMS_API_TIMEOUT=30
SMS_MAX_RETRIES=3
SMS_RETRY_DELAY=5
SMS_MAX_MESSAGE_LENGTH=160
SMS_ENABLE_LOGGING=true
```

### Step 2: Run Database Migration

```bash
php artisan migrate
```

This creates the `sms_logs` table for comprehensive SMS tracking.

### Step 3: Test Configuration

```bash
# Test configuration only
php artisan sms:test --config-only

# Send test SMS
php artisan sms:test 966501234567
```

### Step 4: Access Admin Interface

Visit `/admin/sms` to access the SMS management dashboard with:
- Configuration status
- Real-time statistics
- Test SMS functionality
- Service information

## API Usage Examples

### Basic SMS Sending

```php
use App\Services\SmsService;

$smsService = app(SmsService::class);

$result = $smsService->sendSms(
    '966501234567',  // Mobile number
    'Your OTP is: 1234',  // Message
    [
        'purpose' => 'otp',  // Optional: for categorization
        'reference_id' => 'user-123',  // Optional: for tracking
    ]
);

if ($result['success']) {
    // SMS sent successfully
    logger('SMS sent successfully', $result['data']);
} else {
    // Handle error
    logger('SMS failed', ['error' => $result['message']]);
}
```

### With Notification System

The existing notification system continues to work unchanged. The `SmsChannel` automatically uses the new service.

```php
// In your notification class
public function via($notifiable)
{
    return [SmsChannel::class];
}

public function toSms($notifiable)
{
    return 'Your OTP is: ' . $this->otp;
}
```

## Backward Compatibility

✅ **All existing functionality preserved**
- Notification classes work unchanged
- SMS channel continues to function
- API responses maintain same structure
- Queue jobs continue working

## New Features

### 1. Enhanced Logging
Every SMS is logged with:
- Request/response data
- Retry attempts
- Error details
- Performance metrics

### 2. Statistics & Monitoring
```php
$stats = $smsService->getStatistics();
// Returns: total, sent, failed, success_rate, today_sent, etc.
```

### 3. Mobile Number Intelligence
Automatically formats Saudi mobile numbers:
- `0501234567` → `966501234567`
- `501234567` → `966501234567`
- `+966501234567` → `966501234567`

## Future Enhancements

### 1. SMS Templates
```php
// Planned feature
$smsService->sendTemplate('otp_template', $mobile, [
    'code' => '1234',
    'expiry' => '10 minutes'
]);
```

### 2. Bulk SMS
```php
// Planned feature
$smsService->sendBulk([
    ['mobile' => '966501234567', 'message' => 'Message 1'],
    ['mobile' => '966509876543', 'message' => 'Message 2'],
]);
```

### 3. Scheduled SMS
```php
// Planned feature
$smsService->schedule('966501234567', 'Reminder message', Carbon::tomorrow());
```

### 4. SMS Webhooks
Plan to support delivery status webhooks from ConnectSaudi for real-time status updates.

### 5. Multi-Language Support
Support for Arabic SMS with proper encoding and character counting.

### 6. SMS Campaigns
Admin interface for creating and managing SMS marketing campaigns.

### 7. Rate Limiting
Built-in rate limiting to prevent spam and respect ConnectSaudi API limits.

## Security Considerations

1. **Environment Variables**: All sensitive data stored in environment variables
2. **Request Validation**: All inputs validated before processing
3. **Error Handling**: Errors logged but sensitive data masked
4. **Rate Limiting**: Built-in retry logic prevents API abuse
5. **Access Control**: Admin interface restricted to authorized users

## Monitoring & Alerts

### Built-in Monitoring
- SMS delivery rates
- API response times
- Error patterns
- Usage statistics

### Recommended Alerts
1. SMS failure rate > 10%
2. API response time > 10 seconds
3. Daily SMS volume unusual spikes
4. Configuration errors

## Troubleshooting

### Common Issues

1. **Configuration Invalid**
   ```bash
   php artisan sms:test --config-only
   ```

2. **SMS Not Sending**
   - Check credentials in `.env`
   - Verify all required fields are configured
   - Test with admin interface

3. **High Failure Rate**
   - Check SMS logs: `/admin/sms`
   - Verify mobile number formats
   - Review error messages in logs

### Debug Commands

```bash
# Test configuration
php artisan sms:test --config-only

# Send test SMS
php artisan sms:test 966501234567 --message="Test from CLI"

# View logs
tail -f storage/logs/laravel.log | grep SMS
```

## Performance Optimization

1. **Queue Integration**: SMS sending is already queued
2. **Database Indexing**: SMS logs table has optimized indexes
3. **Caching**: Configuration cached for performance
4. **Retry Logic**: Exponential backoff prevents API overload

## ConnectSaudi API Details

The service uses the ConnectSaudi SMS API with the following format:
```
https://sms.connectsaudi.com/sendurl.aspx?user=xxxxxxxx&pwd=xxxxxxxx&senderid=Advance Dig&mobileno=966501234567&msgtext=Hello&priority=High&CountryCode=966
```

Parameters:
- `user`: Your ConnectSaudi username
- `pwd`: Your ConnectSaudi password
- `senderid`: SMS sender ID (configurable)
- `mobileno`: Recipient mobile number
- `msgtext`: SMS message content
- `priority`: Message priority (High/Normal/Low)
- `CountryCode`: Country code (966 for Saudi Arabia)

## Conclusion

The new SMS system provides:
- ✅ **Reliable** message delivery with retry logic
- ✅ **Optimized** specifically for ConnectSaudi API
- ✅ **Monitorable** with comprehensive logging and statistics
- ✅ **Maintainable** with clean, documented code
- ✅ **Future-ready** with extensible design

The migration maintains full backward compatibility while providing enhanced functionality and reliability optimized for the ConnectSaudi SMS service. 