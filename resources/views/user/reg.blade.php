@extends('index.layouts.shop')

@section('title', '注册')
@section('content')
    <!-- register -->
    <div class="pages section">
        <div class="container">
            <div class="pages-head">
                <h3>REGISTER</h3>
            </div>
            <div class="register">
                <div class="row">
                    <form class="col s12" >
                        @csrf
                        <div class="input-field">
                            <input type="text" placeholder="手机号" class="validate" id="u_phone" name="tel" required>
                        </div>
                        <div class="input-field">
                            <input type="text" placeholder="短信验证码" class="validate" id="code" name="code" >
                            <button type="button" class="btn btn-success">获取短信验证码</button>
                        </div>
                        <div class="input-field">
                            <input type="text" placeholder="用户名" class="validate" id="u_pwd" name="user_name" required>
                        </div>
                        <div class="input-field">
                            <input type="password" placeholder="密码" class="validate" id="u_pwd" name="password" required>
                        </div>
                        <div class="input-field">
                            <input type="password" placeholder="确认密码" class="validate" id="repwd"  name="repwd" required>
                        </div>
                        <div><input type="button" class="btn button-default" id="reg" value="REGISTER"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end register -->


    <!-- loader -->
    <div id="fakeLoader"></div>
    <!-- end loader -->
    <script src="/adm/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="/adm/plugins/bootstrap/js/bootstrap.min.js"></script>

    <script>
        $('button').click(function () {
            var name = $('input[name="tel"]').val();
            var mobilereg = /^1[3|5|6|7|8|9]\d{9}$/;
            if(mobilereg.test(name)){
                //发送手机号验证码
                $.get('/reg/sendSMS',{name:name},function (res) {
                    if(res.code=='00001'){
                        alert(res.msg);
                    }
                    if(res.code=='00000'){
                        alert(res.msg);
                    }
                    if(res.code=='00002'){
                        alert(res.msg);
                    }
                },'json');
                return;
            }
            alert('请输入正确的手机号');
            return;

        });
        $('#reg').click(function () {
            var tel = $('input[name="tel"]').val();
            var user_name = $('input[name="user_name"]').val();
            var code = $('input[name="code"]').val();
            var password = $('input[name="password"]').val();
            var repwd = $('input[name="repwd"]').val();
            $.post('/reg_do',{tel:tel,user_name:user_name,code:code,password:password,repwd:repwd},function (result) {
                if(result.code=='00001'){
                    alert(result.msg);
                }
                if(result.code=='00002'){
                    alert(result.msg);
                }
                if(result.code=='00003'){
                    alert(result.msg);
                }
                if(result.code=='00004'){
                    alert(result.msg);
                }
                if(result.code=='00005'){
                    alert(result.msg);
                }
                if(result.code=='00000'){
                    location.href = "/login"
                }else{
                    alert(result.msg);
                }
            },'json')
        });
    </script>

@endsection
