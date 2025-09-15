#!/bin/bash

# 测试运行脚本

echo "🚀 开始运行测试套件..."

# 检查PHP版本
echo "📋 PHP版本信息:"
php --version

# 安装依赖
echo "📦 安装依赖..."
composer install --no-dev --optimize-autoloader

# 安装测试依赖
echo "🧪 安装测试依赖..."
composer install --dev

# 运行单元测试
echo "🔬 运行单元测试..."
composer test-unit

# 运行集成测试
echo "🔗 运行集成测试..."
composer test-integration

# 运行功能测试
echo "⚡ 运行功能测试..."
composer test-feature

# 运行所有测试
echo "🎯 运行完整测试套件..."
composer test

# 生成覆盖率报告
echo "📊 生成覆盖率报告..."
composer test-coverage

echo "✅ 测试完成！"
echo "📁 覆盖率报告位置: coverage/index.html"

