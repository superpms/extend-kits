# KitFile

源码：`composer/pms/extend-kits/src/pms/program/kits/KitFile.php`

`KitFile` 是轻量数据容器，继承 `pms\OptionsAccess`。

## 行为

`KitFile` 覆盖了 `get()`：

- key 不存在时返回默认值。
- value 是数组时，自动包装成新的 `KitFile`。
- value 是标量或对象时直接返回。

这让 `kit.json` 和 `kit.extra.json` 中的嵌套数组可以用属性链访问。

## `restore()`

```php
protected function restore(array $data): static
```

`restore()` 直接用数组恢复内部数据。`KitFileSource` 在读取 extra 或返回默认数组时会使用它。

## 与 OptionsAccess 的关系

`OptionsAccess` 提供：

- `toArray()`
- `jsonSerialize()`
- `ArrayAccess`
- `Iterator`
- `Countable`
- `__get()` / `__set()`

因此 `KitFile` 可以同时作为对象、数组、可迭代配置使用。
