sqlite3 /var/www/nichoir.db << EOS
	CREATE TABLE IF NOT EXISTS users (login TINY TEXT PRIMARY KEY, password TEXT);
	CREATE TABLE IF NOT EXISTS Capt_IR (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FConnector TEXT, FStatus TEXT, FTime LONG, FTreated INTEGER DEFAULT 0, FID_Pair LONG);
	CREATE TABLE IF NOT EXISTS meteo (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, tempExt TEXT, humExt TEXT, tempInt TEXT, humInt TEXT);
	CREATE TABLE IF NOT EXISTS meteo2 (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, tempExt TEXT);
	CREATE TABLE IF NOT EXISTS InOut_IR (FDatim DATETIME DEFAULT CURRENT_TIMESTAMP, FStatus TEXT, FTime LONG);
	CREATE TABLE IF NOT EXISTS Capt_cap (dateHeure DATETIME DEFAULT CURRENT_TIMESTAMP, connecteur TEXT, valeur LONG);
	CREATE TABLE IF NOT EXISTS config (setting TINY TEXT PRIMARY KEY, value TINY TEXT, priority INTEGER, valueType);
	CREATE TABLE IF NOT EXISTS configRange (setting TINY TEXT, rangeValue TINY TEXT);
	CREATE TABLE IF NOT EXISTS configAlias (alias TINY TEXT, aliasValue TINY TEXT, setting TINY TEXT, settingValue TINY TEXT);
	CREATE TABLE IF NOT EXISTS
	location
		(	location TINY TEXT PRIMARY KEY,
			value TINY TEXT,
			priority INTEGER,
			valueType TINY TEXT);
EOS
adminPwd=$(printf '%s' "admin" | md5sum | cut -d ' ' -f 1)
sqlite3 /var/www/nichoir.db << EOS
	INSERT INTO users ('login', 'password') VALUES ('admin', '$adminPwd');
EOS
sqlite3 /var/www/nichoir.db << EOS
INSERT INTO config ('setting', 'value', 'priority', 'valueType')	VALUES ('imageSize', 'medium', 0, 'discreet');
INSERT INTO config ('setting', 'value', 'priority', 'valueType') VALUES ('quality', '75', 0, 'range');
INSERT INTO config ('setting', 'value', 'priority', 'valueType') VALUES ('imageTypeDetection', 'picture', 5, 'discreet');
INSERT INTO config ('setting', 'value', 'priority', 'valueType')	VALUES ('threshold', '10', 6, 'range');
INSERT INTO config ('setting', 'value', 'priority', 'valueType') VALUES ('on_motion_detected', 'email', 0, 'email');
INSERT INTO config ('setting', 'value', 'priority', 'valueType') VALUES ('imageTypeInterval', 'off', 3, 'discreet');
INSERT INTO config ('setting', 'value', 'priority', 'valueType') VALUES ('ffmpeg_timelapse_mode', 'daily', 4, 'discreet');
INSERT INTO config ('setting', 'value', 'priority', 'valueType') VALUES ('snapshotInterval', '0', 4, 'range');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageSize', 'low');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageSize', 'medium');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageSize', 'high');

INSERT INTO configRange ('setting', 'rangeValue') VALUES ('quality', '0');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('quality', '100');

INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageTypeDetection', 'off');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageTypeDetection', 'picture');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageTypeDetection', 'video');

INSERT INTO configRange ('setting', 'rangeValue') VALUES ('threshold', '5');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('threshold', '99');

INSERT INTO configRange ('setting', 'rangeValue') VALUES ('snapshotInterval', '0');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('snapshotInterval', '3600');

INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'none');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'hourly');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'daily');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'weekly-sunday');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'weekly-monday');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('ffmpeg_timelapse_mode', 'monthly');

INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageTypeInterval', 'off');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageTypeInterval', 'picture');
INSERT INTO configRange ('setting', 'rangeValue') VALUES ('imageTypeInterval', 'video');


INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'low', 'width', '480') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'low', 'height', '360') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'medium', 'width', '640') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'medium', 'height', '480') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'high', 'width', '1280') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageSize', 'high', 'height', '960') ;

INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageTypeDetection', 'off', 'output_pictures', 'off') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageTypeDetection', 'off', 'ffmpeg_output_movies', 'off') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageTypeDetection', 'picture', 'output_pictures', 'on') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageTypeDetection', 'picture', 'ffmpeg_output_movies', 'off') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageTypeDetection', 'video', 'output_pictures', 'off') ;
INSERT INTO configAlias ('alias', 'aliasValue', 'setting', 'settingValue') VALUES ('imageTypeDetection', 'video', 'ffmpeg_output_movies', 'on') ;
EOS

sqlite3 /var/www/nichoir.db << EOS
	INSERT INTO location ('location', 'value', 'priority', 'valueType')	VALUES ('street', '', 0, 'text');
	INSERT INTO location ('location', 'value', 'priority', 'valueType') VALUES ('houseNumber', '', 0, 'text');
	INSERT INTO location ('location', 'value', 'priority', 'valueType') VALUES ('postalCode', '', 0, 'integer');
	INSERT INTO location ('location', 'value', 'priority', 'valueType')	VALUES ('city', '', 0, 'text');
	INSERT INTO location ('location', 'value', 'priority', 'valueType') VALUES ('country', '', 0, 'text');
	INSERT INTO location ('location', 'value', 'priority', 'valueType') VALUES ('xCoord', '', 0, 'long');
	INSERT INTO location ('location', 'value', 'priority', 'valueType') VALUES ('yCoord', '', 0, 'long');
	INSERT INTO location ('location', 'value', 'priority', 'valueType') VALUES ('zCoord', '', 0, 'long');
EOS
