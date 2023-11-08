$(document).ready(function(){

    $("#REG-STEP_ONE").submit(function (e){
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: "/registratsiya/ajax.php",
            data: $(this).serialize(),
            dataType: "json",
            encode: true,
        }).done(function (data) {

            console.log(data);

            var content = '';

            if(data.error == 5) {
                content = $('<strong style="color: red">Пользователь с таким эмейлом существует, выслать повторно письмо для регистрации ?</strong><br /><br /> <input type="submit" class="btn btn-primary" name="Repeat" value="Запросить повторно письмо">');
                $('input[name=TYPE]').val('REPEAT-SEND');
            }

            if(data.error == 6) {
                content = $('<strong style="color: red">Пользователь с таким эмейлом существует и подтвержден, вы можете восстановить пароль если забыли</strong>');
            }

            if(data.error == 0) {
                content = $('<strong style="color: red">'+data.message+'</strong>');
            }

            if(data.succes == 1) {
                content = $('<strong style="color: red">'+data.message+'</strong>');
            }


            $('input[name=Register]').parent().html(content);
        });


    });

    $("#REG-STEP_SAVE").submit(function (e){
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: "/registratsiya/ajax.php",
            data: $(this).serialize(),
            dataType: "json",
            encode: true,
        }).done(function (data) {

            if(data.succes == 5) {
                console.log('полностью пройдена регистрация');
                window.location.href = '/';
            }

            if(data.error == 1) {
                var content = '';
                for (var key in data.messages) {
                    var value = data.messages[key];

                    content = $('<strong style="color: red">'+value+'</strong><br/>');
                }


                $('.errortext').html(content);
            }
        });


    });


});