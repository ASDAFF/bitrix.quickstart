# inherit

Блок предоставляет функцию, реализующую механизмы для объявления и наследования классов.

## Обзор

### Способы использования функции

| Способ | Сигнатура | Тип возвращаемого значения | Описание |
| ----- | --------- | --------------------- | -------- |
| <a href="#runmode-declare">Объявление базового класса</a> | inherit(<br>`props {Object}`, <br>`[staticProps {Object}]`) | `Function` | Служит для создания (декларации), базового класса на основе свойств объекта. |
| <a href="#runmode-extend">Создание производного класса</a> | inherit(<br>`BaseClass {Function} `&#124;` {Array}`, <br>`props {Object}`, <br>`[staticProps {Object}]`) | `Function` | Позволяет наследовать и доопределять свойства и методы базового класса. |

### Специальные поля объявляемого класса

| Имя | Тип данных | Описание |
| --- | ---------- | -------- |
| <a href="#constructor">__constructor</a> | `Function` | Функция, которая будет вызвана в ходе создании экземпляра класса. |

### Специальные поля экземпляра объявляемого класса

| Поле | Тип данных | Описание |
| ---- | ---------- | -------- |
| <a href="#self">__self</a> | `*` | Позволяет получить доступ к классу из его экземпляра. |
| <a href="#base">__base</a> | `Function` | Позволяет внутри производного класса использовать методы базового (supercall). |

### Публичные технологии блока

Блок реализован в технологиях:

* `vanilla.js`

## Описание

Функция `inherit` позволяет:

* создавать класс по декларации;
* задавать метод-конструктор;
* использовать миксины;
* вызывать методы базовой реализации (super call);
* получать доступ к статическим свойствам класса из его экземпляра.

Блок является основой механизма наследования блоков в `bem-core`.

Функция полиморфна и, в зависимости от типа первого аргумента, может быть использована для:

* тип `Object` – объявления базового класса.
* тип `Function` – создания производного класса на основе базового.

Сигнатуры других аргументов функции зависят от способа выполнения.

### Способы использования функции

<a name="runmode-declare"></a>

#### Объявление базового класса

Способ позволяет объявить базовый класс, передав функции объект со свойствами класса.

**Принимаемые аргументы:**

* `props {Object}` – объект с собственными свойствами базового класса. Обязательный аргумент.
* [`staticProps {Object}`] – объект со статическими свойствами базового класса.

**Возвращаемое значение:** `Function`. Полностью сформированный класс.

```js
modules.require(['inherit'], function(inherit) {

var props = {}, // объект свойств базового класса
    baseClass = inherit(props); // базовый класс

});
```

##### Базовый класс со статическими свойствами

Свойства объекта `staticProps` добавляются как статические к создаваемому классу.

Пример:

```js
modules.require(['inherit'], function(inherit) {

var A = inherit(props, {
    callMe : function() {
        console.log('mr.Static');
    }
});

A.callMe(); // mr.Static

});
```

##### Специальные поля объявляемого класса

<a name="constructor"></a>

###### Поле `__constructor`

Тип: `Function`.

Объект собственных свойств базового класса может содержать зарезервированное свойство `__constructor` – функцию, которая будет автоматически вызвана при создании экземпляра класса.

Пример:

```js
modules.require(['inherit'], function(inherit) {

var A = inherit({
        __constructor : function(property) { // конструктор
            this.property = property;
        },

        getProperty : function() {
            return this.property + ' of instanceA';
        }
    }),
    aInst = new A('Property');

aInst.getProperty(); // Property of instanceA

});
```

<a name="runmode-extend"></a>

#### Создание производного класса

Способ позволяет создать производный класс на основе базового класса и объектов статических и собственных свойств.

**Принимаемые аргументы:**

* `BaseClass {Function} | {Array}` – базовый класс. Может быть массивом функций-миксинов. Обязательный аргумент.
* `props {Object}` – собственные свойства (добавляются к прототипу). Обязательный аргумент.
* [`staticProps {Object}`] – статические свойства.

Если один из объектов содержит свойства, которые уже есть в базовом классе – свойства базового класса будут переопределены.

**Возвращаемое значение:** `Function`. Производный класс.

Пример:

```js
modules.require(['inherit'], function(inherit) {

var A = inherit({
    getType : function() {
        return 'A';
    }
});

// класс, производный от A
var B = inherit(A, {
    getType : function() { // переопределение + 'super' call
        return this.__base() + 'B';
    }
});

var instanceOfB = new B();

instanceOfB.getType(); // возвращает 'AB'

});
```

##### Создание производного класса с миксинами

При объявлении производного класса можно указать дополнительный набор функций. Их свойства будут примешаны к создаваемому классу. Для этого первым аргументом `inherit` нужно указать массив, первым элементом которого должен быть базовый класс, а последующими – примешиваемые функции.

Пример:

```js
modules.require(['inherit'], function(inherit) {

var A = inherit({
    getA : function() {
        return 'A';
    }
});

var B = inherit({
    getB : function() {
        return 'B';
    }
});

// класс, производный от A и B
var C = inherit([A, B], {
    getAll : function() {
        return this.getA() + this.getB();
    }
});

var instanceOfC = new C();

instanceOfC.getAll(); // возвращает 'AB'

});
```

##### Специальные поля экземпляра объявляемого класса

<a name="self"></a>

###### Поле `__self`

Тип: `*`.

Позволяет получить доступ к классу из его экземпляра.

Пример:

```js
modules.require(['inherit'], function(inherit) {

var A = inherit({
        getStaticProperty : function() {
            return this.__self.staticMethod; // доступ к статическим методам
        }
    }, {
        staticProperty : 'staticA',

        staticMethod : function() {
            return this.staticProperty;
        }
    }),
    aInst = new A();

aInst.getStaticProperty(); //staticA

});
```

<a name="base"></a>

###### `__base`

Тип: `Function`.

Позволяет внутри производного класса вызывать одноименные методы базового (supercall). При использовании в статическом методе, будет вызван одноименный статический метод базового класса.

Пример:

```js
modules.require(['inherit'], function(inherit) {

var A = inherit({
    getType : function() {
        return 'A';
    }
}, {
    staticProperty : 'staticA',

    staticMethod : function() {
        return this.staticProperty;
    }
});

// класс, производный от A
var B = inherit(A, {
    getType : function() { // переопределение + 'super' call
        return this.__base() + 'B';
    }
}, {
    staticMethod : function() { // статическое переопределение + 'super' call
        return this.__base() + ' of staticB';
    }
});

var instanceOfB = new B();

instanceOfB.getType(); // возвращает 'AB'
B.staticMethod(); // возвращает 'staticA of staticB'

});
```

<a name="extra-examples"></a>

### Дополнительные примеры

Дополнительные примеры смотрите в репозитории библиотеки [inherit](https://github.com/dfilatov/inherit).
