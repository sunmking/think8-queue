@echo off
echo 🚀 开始运行测试套件...

echo 📋 PHP版本信息:
php --version

echo 📦 安装依赖...
composer install --no-dev --optimize-autoloader

echo 🧪 安装测试依赖...
composer install --dev

echo 🔬 运行单元测试...
composer test-unit

echo 🔗 运行集成测试...
composer test-integration

echo ⚡ 运行功能测试...
composer test-feature

echo 🎯 运行完整测试套件...
composer test

echo 📊 生成覆盖率报告...
composer test-coverage

echo ✅ 测试完成！
echo 📁 覆盖率报告位置: coverage/index.html
pause

