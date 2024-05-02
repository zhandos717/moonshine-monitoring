#!/bin/bash

# Получаем статистику виртуальной памяти
VM_STATS=$(vm_stat)

# Получаем количество страниц свободной памяти
FREE_PAGES=$(echo "$VM_STATS" | grep 'Pages free' | awk '{ print $3 }' | tr -d '.')

# Получаем количество активных страниц
ACTIVE_PAGES=$(echo "$VM_STATS" | grep 'Pages active' | awk '{ print $3 }' | tr -d '.')

# Получаем количество неактивных страниц
INACTIVE_PAGES=$(echo "$VM_STATS" | grep 'Pages inactive' | awk '{ print $3 }' | tr -d '.')

# Получаем размер страницы в байтах (обычно это 4096)
PAGE_SIZE=$(vm_stat | grep 'page size of' | awk '{ print $8 }' | tr -d '.')

# Вычисляем общее количество свободной памяти (в мегабайтах)
TOTAL_FREE=$(echo "$FREE_PAGES + $INACTIVE_PAGES" | bc)
TOTAL_FREE_MB=$(echo "$TOTAL_FREE * $PAGE_SIZE / 1024 / 1024" | bc)

# Вычисляем общее количество используемой памяти (в мегабайтах)
TOTAL_USED_MB=$(echo "$ACTIVE_PAGES * $PAGE_SIZE / 1024 / 1024" | bc)

# Вычисляем общее количество памяти (в мегабайтах)
TOTAL_MEM_MB=$(echo "$TOTAL_FREE_MB + $TOTAL_USED_MB" | bc)

# Вычисляем процент использования памяти
echo $(awk "BEGIN{print $TOTAL_USED_MB / $TOTAL_MEM_MB * 100}")
