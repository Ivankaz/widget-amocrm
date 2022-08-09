define(['jquery', 'underscore', 'twigjs'], function ($, _, Twig) {
    var CustomWidget = function () {
        var self = this, system = self.system;

        this.callbacks = {
            // отрисовка виджета
            // вызывается при сборке виджета
            render: function () {
                console.log('render');
                console.log('self.system().area: ' + self.system().area);
                return true;
            },
            // сбор необходимой информации, взаимодействие со сторонним сервером
            // вызывается сразу после render одновременно с bind_actions
            init: function () {
                console.log('init');
                return true;
            },
            // навешивает события на действия пользователя
            bind_actions: function () {
                console.log('bind_actions');
                return true;
            },
            // вызывается при щелчке на иконку виджета в области настроек
            // может использоваться для добавления на страницу модального окна
            settings: function () {
                console.log('settings');
                return true;
            },
            // вызывается:
            // 1. при щелчке пользователя на кнопке “Установить/Сохранить” в настройках виджета
            // 2. при отключении виджета
            // можно использовать для отправки введенных в форму данных и смены статуса виджета
            onSave: function () {
                console.log('onSave');
                return true;
            },
            // вызывается:
            // 1. при отключении виджета через меню его настроек
            // 2. при переходе между областями отображения виджета
            // может использоваться для удаления из DOM всех элементов виджета, если он был отключен
            destroy: function () {
                console.log('destroy');
            },
            contacts: {
                // выбрали контакты в списке и кликнули по названию виджета
                selected: function () {
                    console.log('contacts');
                }
            },
            leads: {
                // выбрали сделки в списке и кликнули по названию виджета
                selected: function () {
                    console.log('leads');
                }
            },
            tasks: {
                // выбрали задачи в списке и кликнули по названию виджета
                selected: function () {
                    console.log('tasks');
                }
            }
        };

        return this;
    };

    return CustomWidget;
});