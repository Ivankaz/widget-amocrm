define(['jquery', 'underscore', 'twigjs', 'lib/components/base/modal'], function ($, _, Twig, Modal) {
    var CustomWidget = function () {
        var self = this, system = self.system;

        this.callbacks = {
            // отрисовка виджета
            // вызывается при сборке виджета
            render: function () {
                console.log('render');

                // если открыли карточку сделки
                if (self.system().area == 'lcard') {
                    let $widgets_block = $('#widgets_block');

                    // если кнопки ещё нет
                    if ($widgets_block.find('#show_products_button').length == 0) {
                        // путь к файлу стилей виджета
                        let stylePath = self.params.path + '/style.css';
                        // подключаю стили
                        $('head').append('<link href="' + stylePath + '" rel="stylesheet">');

                        // добавляю кнопку в правую панель
                        $widgets_block.append(
                            self.render({ref: '/tmpl/controls/button.twig'}, {
                                id: 'show_products_button',
                                text: 'Посмотреть товары'
                            })
                        );
                    }
                }

                return true;
            },
            // сбор необходимой информации, взаимодействие со сторонним сервером
            // вызывается сразу после render одновременно с bind_actions
            init: function () {
                console.log('init');

                // таблица с товарами
                self.productsTable = '';

                // строки таблицы с товарами
                let productRows = '';
                // получаю товары со своего сервера
                $.getJSON('https://ivankazakov.blog/public/amocrm/first-widget/widget.php', function (products) {
                    if (products.length === 0) {
                        // если товаров нет
                        productRows += '<tr><td colspan="2">Товаров нет</td></tr>';
                    } else {
                        // если есть товары, добавляю их в таблицу
                        for (let product of products) {
                            productRows += '<tr><td>' + product.name + '</td><td>' + product.quantity + '</td></tr>';
                        }

                        // заголовок таблицы с товарами
                        let thead = '<thead><tr><th>Название</th><th>Количество</th></tr></thead>';
                        // строки таблицы с товарами
                        let tbody = '<tbody>' + productRows + '</tbody>';
                        // таблица с товарами
                        self.productsTable = '<table>' + thead + tbody + '</table>';
                    }
                });

                return true;
            },
            // навешивает события на действия пользователя
            bind_actions: function () {
                console.log('bind_actions');

                // событие клика по кнопке просмотра товаров
                $('#widgets_block #show_products_button').on('click', function () {
                    // показываю модальное окно с товарами
                    var modal = new Modal({
                        class_name: 'products-modal-window',
                        init: function ($modal_body) {
                            $modal_body
                                .trigger('modal:loaded') // запускаю отображение модального окна
                                .html(self.productsTable) // добавляю вёрстку
                                .trigger('modal:centrify');  // центрирую модальное окно
                        },
                        destroy: function () {
                        }
                    });
                });

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