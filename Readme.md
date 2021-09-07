**************Web serwer for rasperberry pi**************

This is the interface for read and write I/O pin and I2C. The primary use is read data and store it in Mysql (MariaDB) database.

This is a begining project and it will be continously updated.

How script has to be work:
When a session is started the session id is stored in database and changeing after some time periode (15 min). When a session id is changed it is deleted from database too. The database is periodicaly called for cleaning expired session id.
Server uses cookies with expired time 1 hour.



