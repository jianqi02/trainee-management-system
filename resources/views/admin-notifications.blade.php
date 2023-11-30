@extends('layouts.admin')
@section('pageTitle', 'Notifications')

@section('content')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=devide-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body>
        <div class="notification-container">
            <h1>Notifications</h1>
            <form action="{{ route('mark-all-notifications-as-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary notification-btn" style="margin-bottom: 10px;">Mark All as Read</button>
            </form>
            <ul class="list-group">
                @forelse($notifications as $notification)
                    @php
                        $notificationClass = $notification->read_at ? 'read-notification' : 'unread-notification';
                        $notificationData = json_decode($notification->data);
                    @endphp
                    <li class="list-group-item {{ $notificationClass }}" style="{{ $notificationData->style ?? '' }}">
                        <div class="horizontal-wrapper">
                            {{ $notificationData->data ?? '' }}   
                            @if (!$notification->read_at)
                                <form action="{{ route('mark-notification-as-read', ['id' => $notification->id]) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-link notification-btn">Mark as Read</button>
                                </form>
                            @endif
                            <span class="badge badge-primary badge-pill float-right" style="position: absolute; right: 0; background-color: grey; border-radius: 4px;">
                                {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No notifications yet.</li>
                @endforelse
            </ul>
            {{ $notifications->links() }}
        </div>
    </body>
</html>

@endsection