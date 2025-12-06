#!/bin/bash
# ローカル環境切り替えスクリプト（互換性レイヤー）
# 
# 注意: このスクリプトは廃止予定です
# 新しい統合スクリプト switch-environment.sh を使用してください
#
# 使用例:
#   ./switch-environment.sh --to-local

echo "=========================================="
echo "注意: このスクリプトは廃止予定です"
echo "新しいスクリプトに転送しています..."
echo "=========================================="
echo ""

# スクリプトのディレクトリを取得
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# 新しいスクリプトを実行
exec "${SCRIPT_DIR}/switch-environment.sh" --to-local "$@"
