<div
        class="max-w-4xl mx-auto relative mb-6"
        x-data="{ activeSlide: 1, slides: [{{ slideNumbers($images) }}] }"
>

    @foreach($images as $index => $image)
        @if(substr($image, -4, 4) == '.mp4')
                <video controls class="d-block w-full" x-show="activeSlide === {{ $index + 1 }}">
                    <source src="{{ $image }}" type="video/mp4">
                </video>
        @else

            <img src="{{ $image }}" alt="" x-show="activeSlide === {{ $index + 1 }}">
        @endif
    @endforeach

    <!-- Prev/Next Arrows -->
    {{--<div class="absolute inset-0 flex justify-between">--}}
            {{--<div class="flex items-center justify-start w-1/2">--}}
                    {{--<button--}}
                            {{--class="bg-gray-100 text-gray-700 hover:text-orange-500 font-bold hover:shadow rounded-full w-12 h-12 -ml-6"--}}
                            {{--x-on:click="activeSlide = activeSlide === 1 ? slides.length : activeSlide - 1">--}}
                            {{--&#8592;--}}
                    {{--</button>--}}
            {{--</div>--}}
            {{--<div class="flex items-center justify-end w-1/2">--}}
                    {{--<button--}}
                            {{--class="bg-gray-100 text-gray-700 hover:text-orange-500 font-bold hover:shadow rounded-full w-12 h-12 -mr-6"--}}
                            {{--x-on:click="activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1">--}}
                            {{--&#8594;--}}
                    {{--</button>--}}
            {{--</div>--}}
    {{--</div>--}}

    <!-- Buttons -->
        <div class="absolute w-full flex items-center justify-center px-4">
            <template x-for="slide in slides" :key="slide">
                <button
                        class="flex-1 w-4 h-2 mt-4 mx-2 mb-0 rounded-full overflow-hidden transition-colors duration-200 ease-out hover:bg-gray-600 hover:shadow-lg"
                        :class="{
              'bg-orange-600': activeSlide === slide,
              'bg-gray-300': activeSlide !== slide
          }"
                        x-on:click="activeSlide = slide"
                ></button>
            </template>
        </div>

</div>