# Script para hacer commits rápidos
# Uso: .\git_commit.ps1 "ad filament: Descripcion del cambio"

param(
    [Parameter(Mandatory=$true)]
    [string]$mensaje
)

Write-Host "📝 Haciendo commit en staging..." -ForegroundColor Cyan

# Agregar todos los cambios
git add .

# Hacer commit
git commit -m $mensaje

# Mostrar estado
Write-Host "`n✅ Commit realizado:" -ForegroundColor Green
git log -1 --oneline

Write-Host "`n📊 Branch actual:" -ForegroundColor Yellow
git branch --show-current
