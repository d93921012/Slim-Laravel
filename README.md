# Slim-Laravel
Slim 2 + Laravel 5.0

This project is a basic application based on Slim 2 with the compoents of Laravel 5.0. Because the goal is to run in Windows server 2003, this project sticks on Slim 2.

The compoents of Laravel 5.0 included in this basic application include config, database, session, cache, and view. In order to increase performance, the session is started up only when it is needed.

這個主要是我自己拿來練功的。原本的 Laravel 整個拉下來，vendor 目錄下的檔案有好幾千個，大約 20~30MB，備份時要花一下時間，才能掃完整個目錄。

Slim 很適合加上各種 component 來打造自己的 framework。在此，主要是加上一些自己常用的 Laravel 的構件。最主要的差別是，將 session 改成要用到時才啟動，因為啟動 session 會讓整個效能減低。Laravel 是一律啟動 session，人多時，session 的目錄下，檔案成長很快。對於只供查詢的網頁，是不需要儲存 session 的。

由於目前，還很多系統仍在 Windows server 2003 上執行，因此選用支援 PHP 5.4 的 slim 2。vendor 目錄下，全部檔案大約 4MB 左右。
