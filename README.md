# Laravel Currency API

這是一個 Laravel 框架的貨幣轉換 API，提供兩種轉換方式：使用固定的匯率資料或使用 ExchangeRate-API 獲取最新匯率。

## 功能

1. **exchange API**: 使用固定的匯率資料做轉換。
2. **exchangerate API**: 使用 ExchangeRate-API 獲取最新匯率做轉換。

## 如何使用

### 本機測試

首先，你需要將專案 clone 到你的本機上：

```bash
git clone https://github.com/wayne79687968/currency_api.git
cd currency_api/
```

然後，啟動 Laravel 伺服器：

```bash
php artisan serve
```

請注意，由於 ExchangeRate-API 不支援本機，所以你只能在本機上測試 `exchange` API。

### 線上測試

你可以使用以下的網址來測試 `exchange` 和 `exchangerate` API：

- `exchange` API: [https://currency.skogkatt.website/api/exchange?source=USD&target=JPY&amount=$1,525](https://currency.skogkatt.website/api/exchange?source=USD&target=JPY&amount=$1,525)
- `exchangerate` API: [https://currency.skogkatt.website/api/exchangerate?source=USD&target=JPY&amount=$1,525](https://currency.skogkatt.website/api/exchangerate?source=USD&target=JPY&amount=$1,525)

## 配置

如果你想使用 `exchangerate` API，你需要在專案根目錄的 `.env` 檔案中設定你的 ExchangeRate-API 金鑰：

```bash
EXCHANGE_RATE_API_KEY="${YOUR_API_KEY}"
```

你可以從 [ExchangeRate-API](https://www.exchangerate-api.com/) 註冊一個帳號來獲得 API 金鑰。
