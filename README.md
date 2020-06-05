# sftp-sync

This hacky script can help keep two sftp server in sync. it looks for new files on server1 and send it to server2 on the same path.

### Usage

Create your .env file and run the script and just run `php sync.php`

### Example 

```
[2020-06-05T04:46:15.989215+01:00] sync.DEBUG: Starting sync of the sftp servers [] []
[2020-06-05T04:46:19.583168+01:00] sync.DEBUG: Looking for files at Outbound/DC [] []
[2020-06-05T04:46:30.219916+01:00] sync.DEBUG: Looking for files at Outbound/GR [] []
[2020-06-05T04:46:34.879030+01:00] sync.DEBUG: Looking for files at Outbound/IF [] []
[2020-06-05T04:46:36.677361+01:00] sync.DEBUG: Copying file Outbound/IF/IF0106202000.csv to staging. [] []
[2020-06-05T04:46:41.814944+01:00] sync.DEBUG: Copying file Outbound/IF/IF0206202000.csv to staging. [] []
[2020-06-05T04:46:48.028226+01:00] sync.DEBUG: Copying file Outbound/IF/IF0306202000.csv to staging. [] []
[2020-06-05T04:46:58.862785+01:00] sync.DEBUG: Copying file Outbound/IF/IF2905202000.csv to staging. [] []
[2020-06-05T04:47:04.600379+01:00] sync.DEBUG: Looking for files at Outbound/SR [] []
[2020-06-05T04:47:05.629079+01:00] sync.DEBUG: 4 files has been copied. [] []
[2020-06-05T04:47:05.629593+01:00] sync.DEBUG: end [] []
```

After you run it, a file will be created syncedFiles.json containing these paths so that next run it ignores it.

```
[2020-06-05T04:48:04.366782+01:00] sync.DEBUG: Starting sync of the sftp servers [] []
[2020-06-05T04:48:12.591907+01:00] sync.DEBUG: Looking for files at Outbound/DC [] []
[2020-06-05T04:48:22.669443+01:00] sync.DEBUG: Looking for files at Outbound/GR [] []
[2020-06-05T04:48:34.890935+01:00] sync.DEBUG: Looking for files at Outbound/IF [] []
[2020-06-05T04:48:37.343777+01:00] sync.DEBUG: Looking for files at Outbound/SR [] []
[2020-06-05T04:48:38.266390+01:00] sync.DEBUG: No files were copied. all in sync. [] []
[2020-06-05T04:48:38.268253+01:00] sync.DEBUG: end [] []
```

Simple as that. 