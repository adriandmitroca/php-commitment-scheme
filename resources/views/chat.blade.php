@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Messages</div>

                <div class="panel-body" id="messages">
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Talk</div>

                <div class="panel-body">
                    <div class="alert alert-danger errors" role="alert" style="display: none"></div>

                    <form action="chat/new" method="POST" id="sendMessage">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <textarea class="form-control" rows="3" name="content" placeholder="Message" required></textarea>
                        </div>
                        <div class="form-group pull-right">
                            <button class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.client_id = '{{ session('client_id') }}';
    console.log(window.client_id);
</script>
@endsection

