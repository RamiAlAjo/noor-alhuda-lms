# Check translation completeness
$enContent = Get-Content "lang\en.json" | ConvertFrom-Json
$arContent = Get-Content "lang\ar.json" | ConvertFrom-Json

$enKeys = $enContent | Get-Member -MemberType NoteProperty | Select-Object -ExpandProperty Name
$arKeys = $arContent | Get-Member -MemberType NoteProperty | Select-Object -ExpandProperty Name

$missingInAr = $enKeys | Where-Object { $_ -notin $arKeys }
$extraInAr = $arKeys | Where-Object { $_ -notin $enKeys }

Write-Host "Keys missing in Arabic: $($missingInAr.Count)"
Write-Host "Extra keys in Arabic: $($extraInAr.Count)"

if ($missingInAr.Count -gt 0) {
    Write-Host "Missing keys:"
    $missingInAr | ForEach-Object { Write-Host "  $_" }
}