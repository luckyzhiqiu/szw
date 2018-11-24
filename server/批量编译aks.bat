@echo off
for /R %%s in (*.aks) do ( 
echo %%s
%akserver_home%\aks\aks.exe %%s 
) 
pause 