<x-app-layout>
    <x-slot name="header">

        <h2 class="font-semibold text-xl mb-4 text-gray-800 leading-tight">
        <div>
            店舗Report詳細
        </div>
        </h2>

        <x-flash-message status="session('status')"/>
        <div class="md:flex ml-8 ">
        <div class="flex">
        <div class="ml-2 mb-2 md:mb-0">
            <button type="button" onclick="location.href='{{ route('report_list') }}'" class="w-32 h-8 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-700 rounded ">店舗Report一覧</button>
        </div>
        <div class="ml-2 mb-2 md:mb-0">
            <button type="button" onclick="location.href='{{ route('shop_index') }}'" class="w-32 h-8 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-700 rounded ">店舗一覧</button>
        </div>
        </div>
        <div class="flex">
        <div class="ml-2 mb-2 md:mb-0">
            <button type="button" onclick="location.href='{{ route('comment_create',['report'=>$report->id]) }}'" class="w-32 h-8 text-center text-sm text-white bg-green-500 border-0 py-1 px-2 focus:outline-none hover:bg-green-700 rounded ">コメント登録</button>
        </div>

        {{--  @foreach ($reports as $report)  --}}
        @if($login_user == $report->user_id)

        <div class="ml-2 mb-0 md:mb-0">
            <button type="button" onclick="location.href='{{ route('report_edit',['report'=>$report->id])}}'" class="w-32 h-8 text-center text-sm text-white bg-green-500 border-0 py-1 px-2 focus:outline-none hover:bg-green-600 rounded ">編集</button>
        </div>
        </div>
        @if ( ($report->created_at) > (\Carbon\Carbon::today()->addDay(-1)) && !($comment_exist))
        {{-- @if ( ($report->created_at) > (\Carbon\Carbon::today()->addDay(-1)) ) --}}
        <div>
        <form id="delete_{{$report->id}}" method="POST" action="{{ route('report_destroy',['report' => $report->id]) }}">
            @csrf
            @method('delete')
            <div class="md:px-4 py-0">
                <div class="p-0 w-full flex ml-2 mt-0 md:mt-0">
                <a href="#" data-id="{{ $report->id }}" onclick="deletePost(this)" class="w-32 text-center text-sm text-white bg-red-500 border-0 py-1 px-2 focus:outline-none hover:bg-red-700 rounded  ">削除</a>
                </div>
            </div>
        </form>
        </div>
        @endif
        @endif
        {{--  @endforeach  --}}
        </div>

    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 bg-white border-b border-gray-200">

                    <form method="get" action=""  enctype="multipart/form-data">

                        <div class="-m-2">
                            <div class="p-2 mx-auto">
                                {{-- @foreach ($reports as $report) --}}

                                <div class="p-2 w-full mx-auto">
                                    <div class="relative">
                                        <label for="date" class="leading-7 text-sm  text-gray-800 ">日付</label>
                                        <div  id="date" name="date" value="{{$report->created_at}}" class="h-8 text-sm w-full bg-gray-100 bg-opacity-50 border rounded focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 outline-none text-gray-700 px-3 leading-8 transition-colors duration-200 ease-in-out">{{\Carbon\Carbon::parse($report->created_at)->format("y/m/d")}}
                                    </div>
                                    <div class="relative">
                                        <label for="user_name" class="leading-7 text-sm  text-gray-800 ">投稿者</label>
                                        <div  id="user_name" name="user_name" value="{{$report->name}}" class="h-8 text-sm w-full bg-gray-100 bg-opacity-50 border rounded focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 outline-none text-gray-700 px-3 leading-8 transition-colors duration-200 ease-in-out">{{$report->name}}
                                    </div>
                                    <div class="relative">
                                        <label for="sh_name" class="leading-7 text-sm  text-gray-800 ">店名</label>
                                        <div  id="sh_name" name="sh_name" value="{{$report->shop_name}}" class="h-8 text-sm w-full bg-gray-100 bg-opacity-50 border rounded focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 outline-none text-gray-700 px-3 leading-8 transition-colors duration-200 ease-in-out">{{$report->shop_name}}
                                    </div>
                                    </div>
                                    <div class="mx-auto mb-1">
                                        <div class="relative">
                                            <label for="information" class="leading-7 text-sm  text-gray-800 ">Report</label>
                                            <div id="information" name="information" rows="10" class="w-full bg-gray-100 bg-opacity-50 border rounded focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 px-3 leading-8 transition-colors duration-200 ease-in-out">{!! nl2br(e($report->report)) !!}</div>
                                        </div>
                                    </div>


                                <div class="px-2 md:w-2/3 mx-auto">
                                <div class="">
                                    <div class="w-full mb-1">
                                        @if(!empty($report->image1))
                                        <a href="{{ route('report_image1',['report'=>$report->id]) }}">
                                        <img src="{{ asset('storage/public/reports/'.$report->image1) }}">
                                        </a>
                                        @endif
                                    </div>
                                    <div class="w-full mb-1">
                                        @if(!empty($report->image2))
                                        <a href="{{ route('report_image2',['report'=>$report->id]) }}">
                                        <img src="{{ asset('storage/public/reports/'.$report->image2) }}">
                                        </a>
                                        @endif
                                        {{-- <img src="{{ asset('storage/reports/'.$report->image2) }}"> --}}
                                    </div>
                                    <div class="w-full mb-1">
                                        @if(!empty($report->image3))
                                        <a href="{{ route('report_image3',['report'=>$report->id]) }}">
                                        <img src="{{ asset('storage/public/reports/'.$report->image3) }}">
                                        </a>
                                        @endif
                                        {{-- <img src="{{ asset('storage/reports/'.$report->image3) }}"> --}}
                                    </div>
                                    <div class="w-full mb-1">
                                        @if(!empty($report->image4))
                                        <a href="{{ route('report_image4',['report'=>$report->id]) }}">
                                        <img src="{{ asset('storage/public/reports/'.$report->image4) }}">
                                        </a>
                                        @endif
                                        {{-- <img src="{{ asset('storage/reports/'.$report->image4) }}"> --}}
                                    </div>
                                </div>
                                </div>


                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="py-0 border">
        <h2>コメント一覧</h2>
        <div class=" mx-auto sm:px-4 lg:px-4 border ">
            <table class="md:w-full bg-white table-auto w-full text-center whitespace-no-wrap">
               <thead>
                    <tr>
                        <th class="w-1/12 md:1/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Id</th>
                        <th class="w-2/12 md:1/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">投稿日</th>
                        <th class="w-2/12 md:2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">投稿者</th>
                        <th class="w-3/12 md:5/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">コメント</th>
                        <th class="w-2/12 md:2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">状態</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($comments as $comment)
                    <tr>
                        <td class="w-1/12 md:1/12 text-sm md:px-4 py-1 text-center"> {{ $comment->id }}</td>
                        <td class="w-2/12 md:1/12 text-sm md:px-4 py-1 text-center"> {{\Carbon\Carbon::parse($comment->updated_at)->format("y/m/d ")}} </td>
                        <td class="w-2/12 md:2/12 text-sm md:px-4 py-1 text-center">{{ $comment->name }}</td>
                        <td class="w-3/12 md:2/12 text-sm md:px-4 py-1 text-left"><a href="{{ route('comment_detail',['comment'=>$comment->id]) }}" class="w-20 h-8 text-indigo-500 ml-2 "  >{{(Str::limit($comment->comment,28)) }} </a></td>
                        @if($comment->comment_reads)
                        <td class="w-2/12 md:2/12 text-sm md:px-4 py-1 text-center">既読</td>
                        @else
                         <td class="w-2/12 md:2/12 text-red-600 text-sm md:px-4 py-1 text-center">未読</td>
                         @endif
                    </tr>
                    @endforeach

                </tbody>

            </table>
            {{-- {{  $comments->appends([
                'co_id'=>\Request::get('co_id'),
                'area_id'=>\Request::get('area_id'),
                'sh_id'=>\Request::get('sh_id'),
                'info'=>\Request::get('info'),
            ])->links()}} --}}
        </div>
    </div>

    <script>
        function deletePost(e) {
        'use strict';
        if (confirm('本当に削除してもいいですか?')) {
        document.getElementById('delete_' + e.dataset.id).submit();
        }
        }
    </script>

</x-app-layout>
