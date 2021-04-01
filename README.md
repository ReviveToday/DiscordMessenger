# WordPress Update Discord Bot
Sends post and page interactions to a designated bot user webhook.

## Options
*There's currently no UI for changing settings. Edit via wp-admin/options.php or directly via a database client.*

Option              | Description
------------------- | -----------
`wpudb_webhook_url` | The bot webhook URL provided by Discord integration.
`wpudb_timer`       | Time of last Discord update, for limiting purposes (hard-coded limit of 1 per minute).
