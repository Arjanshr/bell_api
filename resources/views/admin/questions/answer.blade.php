@extends('adminlte::page')

@section('title', 'Answer Question')

@section('content_header')
    <h1>Answer Question</h1>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <strong>Question:</strong> {{ $question->question }}<br>
                            <strong>Product:</strong> {{ $question->product ? $question->product->name : 'N/A' }}<br>
                            <strong>User:</strong> {{ $question->user ? $question->user->name : 'Guest' }}
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('questions.answer.submit', $question->id) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="answer">Your Answer</label>
                                    <textarea name="answer" id="answer" class="form-control" rows="4" required>{{ old('answer', $question->answer) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Submit Answer</button>
                                <a href="{{ route('questions') }}" class="btn btn-secondary">Back</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
