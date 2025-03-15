@echo off

rem CORE CPU
set NAME=STCORE
php -f ..\scripts\preprocess.php %NAME%.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
..\scripts\macro11 -ysl 32 -yus -m ..\scripts\sysmac.sml -l _%NAME%.lst _%NAME%.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f ..\scripts\lst2bin.php _%NAME%.lst _%NAME%.bin bin 1000
if %ERRORLEVEL% NEQ 0 ( exit /b )
rem --- pack ---
..\scripts\zx0 -q -f _%NAME%.bin _%NAME%.zx0.bin
rem --- clean ---
del _%NAME%.mac
del _%NAME%.bin
rem del _%NAME%.lst

rem MAIN EXECUTABLE
set NAME=SABOT2
php -f ..\scripts\preprocess.php %NAME%.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
..\scripts\macro11 -ysl 32 -yus -m ..\scripts\sysmac.sml -l _%NAME%.lst _%NAME%.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f ..\scripts\lst2bin.php _%NAME%.lst ./release/%NAME%.sav sav
if %ERRORLEVEL% NEQ 0 ( exit /b )
rem --- clean ---
del _%NAME%.mac
rem del _%NAME%.lst

del _STCORE.zx0.bin

rem -- put to disk --
..\scripts\rt11dsk d .\release\sabot2.dsk %NAME%.sav >NUL
..\scripts\rt11dsk a .\release\sabot2.dsk .\release\%NAME%.sav >NUL

echo.
run_ukncbtl.bat