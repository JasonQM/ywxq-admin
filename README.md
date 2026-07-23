# YWXQ Admin

基于 Laravel 13 和 Filament 4 的游戏数据后台。

## 当前功能

- Filament 后台登录、退出。
- 首页数据面板：活跃 DAU、最近 7 天充值、消耗、ROI、待处理预警。
- 数据明细：
  - 每日数据：按日期倒序展示，消耗支持人工编辑，表格底部显示汇总。
  - 留存数据：展示 d1、d3、d7、rd1、rd3、rd7 及对应留存率。
- 异常预警：自动生成数据异常，支持标记已处理或忽略。
- 接口同步：
  - 每日统计接口按 7 天分块拉取。
  - 实名人数接口按天拉取。
  - 默认从 `2026-06-11` 同步到当天。
- 定时任务：每天 `00:30` 执行 `game:sync-stats`。

## 本地使用

```bash
composer install
php artisan key:generate
php artisan migrate --force
php artisan game:make-admin
php artisan game:sync-stats
php artisan serve
```

后台地址：

```text
http://127.0.0.1:8000/admin
```

默认管理员：


```

## 常用命令

```bash
# 同步全部数据，默认从 2026-06-11 到今天
php artisan game:sync-stats

# 同步指定日期范围
php artisan game:sync-stats --start=20260611 --end=20260617

# 创建或重置管理员
php artisan game:make-admin --email=admin@123.com --password=ywxq
```

## 服务器定时任务

服务器需要配置 Laravel 调度器，每分钟执行一次：

```cron
* * * * * cd /var/www/ywxq-admin && php artisan schedule:run >> /dev/null 2>&1
```

Laravel 会在每天 `00:30` 自动触发数据同步。

## 环境变量

复制 `.env.example` 为 `.env`，按服务器环境填写数据库和接口配置。

```env
GAME_STATS_START_DAY=20260611
GAME_STATS_BASE_URL=http://81.69.9.148:8888/call
GAME_STATS_CHUNK_DAYS=7
```
