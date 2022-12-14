@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <section class="page-content">
        <h1>Stuff I Use</h1>

        <p>This is my <a href="https://uses.tech">/uses page</a>. It was last updated December 13, 2022.</p>

        <div class="stuff-list">
            <div>
                <h3><a href="/uses/macbook">MacBook Pro</a></h3>
            </div>

            <div>
                <h3><a href="/uses/display">Display</a></h3>
            </div>

            <div>
                <h3><a href="/uses/keyboard-mouse">Keyboard and Mouse</a></h3>
            </div>

            <div>
                <h3><a href="/uses/webcam">Webcam</a></h3>
            </div>

            <div>
                <h3><a href="/uses/headphones">Headphones</a></h3>
            </div>

            <div>
                <h3><a href="/uses/desk">Desk</a></h3>
            </div>

            <div>
                <h3><a href="/uses/software">Software</a></h3>
            </div>

            <div>
                <h3><a href="/uses/devices">Devices</a></h3>
            </div>

            <div>
                <h3><a href="/uses/services">Services</a></h3>
            </div>

            <div>
                <h3><a href="/uses/cycling">Cycling</a></h3>
            </div>


        </div>
    </section>
@endsection
