# 3gorillas.com bot
Crawler Example for 3gorillas.com to submit content at YIVE

## Gettting started
1. Clone repository using 
```bash
git clone git@github.com:YiveSystems/3gorilla-yive-crawler-example.git
```

2. Edit `run.php` and update API key on YiveApiClient
```php
$yiveClient = new YiveApiClient('YOUR_TOKEN_HERE');
```

3. Run crawler
```bash
php run.php
```
