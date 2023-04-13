@forelse($notes as $note)

    @if(Auth::id() == $note->created_by)
        <div class="chat">
            <div class="chat-avatar">
                <a class="avatar m-0" title='{{$note->fullname}}'>
                    <img src="{{$note->urlpicture}}" alt="avatar" height="40" width="40" />
                </a>
            </div>
            <div class="chat-body">
                <div data-idnote='{{$note->id}}' class="chat-content my-note" style="background: linear-gradient(118deg, #28c76f, #62a780)">
                    <p style="color:gray; font-size:12px;">{{$note->fullname}} | {{$note->created_at}}</p>
                    @if($note->type == 1)
                        <p style="position: static;">{{$note->note}}</p>
                    @else
                        <img style="position: static;" src="{{$note->note}}" alt="avatar" height="300" width="300"/>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="chat chat-left">
            <div class="chat-avatar mt-50">
                <a class="avatar m-0" title='{{$note->fullname}}'>
                    <img src="{{$note->urlpicture}}" alt="avatar" height="40" width="40" />
                </a>
            </div>
            <div class="chat-body">
                <div data-idnote='{{$note->id}}' class="chat-content">
                    <p style="color:gray; font-size:12px;">{{$note->fullname}} | {{$note->created_at}}</p>
                    @if($note->type == 1)
                        <p style="position: static;" >{{$note->note}}</p>
                    @else
                        <img style="position: static;" src="{{$note->note}}" alt="avatar" height="300" width="300"/>
                    @endif
                    
                </div>
            </div>
        </div>
    @endif

@empty
    <div class="start-chat-area" style="border-radius:50%; font-size:4rem; padding:2rem;">
        <span class="mb-1 start-chat-icon feather icon-message-square"></span>
        <h4 class="py-50 px-1 sidebar-toggle start-chat-text">Escribe una nota</h4>
    </div>
@endforelse


