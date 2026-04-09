## Datamind — тестове завдання

Консольний застосунок на **Yii2** з **MongoDB** та **Elasticsearch 7**:

- **Імпорт** XLSX → MongoDB (upsert, без дублів при повторному запуску)
- **Перенос** MongoDB → Elasticsearch (upsert, без дублів при повторному запуску)
- **Звіт (агрегація)**: групування по **області** та **товару**, сума по **кількості**

## 1) Як установити проєкт

Вимоги: **Docker** + **Docker Compose**.

Примітка: **тестував лише на Linux**.

Файл даних вже лежить в проєкті:

- `data/input.xlsx`

Запуск:

```bash
cd /var/www/datamind
make init
make start
```

### Windows (інструкція)

Працювати має так само через Docker, але **я це не тестував**.

- Встанови **Docker Desktop**.
- Відкрий репозиторій у **PowerShell** або **Git Bash**.
- Запусти:

```bash
docker compose -f docker/docker-compose.yml up -d --build
docker compose -f docker/docker-compose.yml exec app bash
```

Далі команди з розділу **2)** виконуються вже всередині контейнера.

## 2) Команди для перевірки функціональності

Усі команди виконуються в контейнері:

```bash
make ssh
```

### 2.1 Імпорт XLSX → MongoDB

```bash
php yii import/import 50
```

Повторний запуск **не створює дублі**: `schema_rows` пишеться через upsert по стабільному `_id`.

### 2.2 Перенос MongoDB → Elasticsearch

```bash
php yii index/index/transfer 500
```

Повторний запуск **не створює дублі**: документи в ES upsert-яться по тому ж `_id`, що й у Mongo.

### 2.3 Звіт (агрегація) з Elasticsearch

```bash
php yii aggregation/report
```

## 3) Бібліотеки та підходи, які використав (і чому)

- **Yii2 DI container**: залежності інжектяться в команди/контролери/сервіси, мінімум глобального стану.
- **MongoDB bulk upsert**: батчеві операції швидші й повторно-запускні без дублів.
- **Elasticsearch PHP client** (`elasticsearch/elasticsearch`) + bulk upsert: швидкий перенос даних, той самий `_id`.
- **PhpSpreadsheet**: читання XLSX. Для стабільної памʼяті використано **chunked read** (фільтр по діапазону рядків).
- **Mojibake repair**: `MojibakeDecoder` декодує оригінальний файл.
