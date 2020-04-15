/* список типов контента для поста */
INSERT into content_types SET type_name = 'post-quote', type_class = 'quote';
INSERT into content_types SET type_name = 'post-text', type_class = 'text';
INSERT into content_types SET type_name = 'post-photo', type_class = 'photo';
INSERT into content_types SET type_name = 'post-link', type_class = 'link';
INSERT into content_types SET type_name = 'post-video', type_class = 'video';
/* придумайте пару пользователей */
INSERT into users SET username = 'Лариса', email = 'larisa@mail.ru', avatar = 'userpic-larisa-small.jpg';
INSERT into users SET username = 'Владик', email = 'vladik@mail.ru', avatar = 'userpic.jpg';
INSERT into users SET username = 'Виктор', email = 'viktor@mail.ru', avatar = 'userpic-mark.jpg';
/* существующий список постов */
INSERT into posts SET title = 'Цитата', post_type = '1', content = 'Мы в жизни любим только раз, а после ищем лишь похожих', author_id = '1', view_count = 30;
INSERT into posts SET title = 'Моя мечта', post_type = '3', content = 'coast-medium.jpg', author_id = '1', view_count = 20;
INSERT into posts SET title = 'Игра престолов', post_type = '2', content = 'Не могу дождаться начала финального сезона своего любимого сериала!', author_id = '2', view_count = 10;
INSERT into posts SET title = 'Наконец, обработал фотки!', post_type = '3', content = 'rock-medium.jpg', author_id = '3', view_count = 999;
INSERT into posts SET title = 'Лучшие курсы', post_type = '4', content = 'www.htmlacademy.ru', author_id = '2', view_count = 500;
/* придумайте пару комментариев к разным постам */
INSERT into comments SET user_id = '2', post_id = '4', content = 'Отличные фото!';
INSERT into comments SET user_id = '1', post_id = '5', content = 'Сейчас там учусь';
/* получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента */
SELECT users.username, posts.content, posts.view_count, content_types.type_name FROM posts JOIN users ON posts.author_id=users.id JOIN content_types ON posts.post_type=content_types.id ORDER  BY view_count;
/* получить список постов для конкретного пользователя */
SELECT content FROM posts WHERE author_id=1;
/* получить список комментариев для одного поста, в комментариях должен быть логин пользователя */
SELECT comments.content, users.username FROM comments JOIN users ON comments.user_id=users.id WHERE comments.post_id=4;
/* добавить лайк к посту */
INSERT into likes SET user_id=1, post_id=3;
/* подписаться на пользователя */
INSERT into subscribe SET follower_id=2, author_id=1;