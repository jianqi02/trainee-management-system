@extends('layouts.admin')
@section('pageTitle', 'Settings')

@section('breadcrumbs', Breadcrumbs::render('settings'))

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }

        .setting-container {
            width: 60%;
            margin: 50px auto;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 30px;
        }

        .setting-container h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 40px;
        }

        .setting-item {
            margin-bottom: 25px;
        }

        .setting-item label {
            display: block;
            font-size: 16px;
            color: #555;
            margin-bottom: 8px;
        }

        .setting-item input[type="text"] {
            width: 80%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .setting-item input[type="text"]:focus {
            border-color: #3498db;
        }

        .setting-item button.add-domain-btn {
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* List of added domains */
        .domains-list {
            list-style: none;
            padding: 0;
            margin-top: 10px;
        }

        .domains-list li {
            background-color: #f9f9f9;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .domains-list li span {
            flex-grow: 1;
        }

        .domains-list li .remove-domain-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
        }
    </style>
</head>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<body>
    <div class="setting-container">
        <h1>Admin Settings</h1>

        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Required Email Domain (Add multiple) -->
            <div class="setting-item">
                <label for="email-domain">Required Email Domain</label>
                <input type="text" id="email-domain-input" placeholder="Enter domain (e.g., example.com)">
                <button type="button" class="add-domain-btn">Add Domain</button>
                <br>
                <small>Set the allowed email domain for registrations</small>
        
                <!-- List of added domains -->
                <ul id="domains-list" class="domains-list">
                    @if($settings->email_domain)
                        @foreach(explode(',', $settings->email_domain) as $domain)
                            <li>
                                <span>{{ trim($domain) }}</span>
                                <button type="button" class="remove-domain-btn">Remove</button>
                                <input type="hidden" name="allowed_domains[]" value="{{ trim($domain) }}">
                            </li>
                        @endforeach
                    @else
                        <li>No domains added yet.</li>
                    @endif
                </ul>
            </div>
        
            <!-- Disable User Registration -->
            <div class="setting-item">
                <label for="disable-registration">
                    <input type="checkbox" id="disable-registration" name="disable_registration" {{ $settings->disable_registration ? 'checked' : '' }}>
                    Disable User Registration
                </label>
                <small>Check this box to disable new user registrations</small>
            </div>

            <div class="setting-item">
                <label for="company-name">
                    <h6>Company Name</h6>
                    <input 
                        type="text" 
                        id="company-name" 
                        name="company_name" 
                        placeholder="Enter your company name here." 
                        value="{{ old('company_name', $settings->company_name ?? '') }}">
                </label>
            </div>
            
        
            <!-- Save Button -->
            <div class="setting-item">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>

    <!-- JavaScript for managing domains -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const domainInput = document.getElementById('email-domain-input');
            const addDomainBtn = document.querySelector('.add-domain-btn');
            const domainsList = document.getElementById('domains-list');
        
            addDomainBtn.addEventListener('click', function () {
                const domain = domainInput.value.trim();
        
                if (domain) {
                    const li = document.createElement('li');
                    li.innerHTML = `<span>${domain}</span> 
                                    <button type="button" class="remove-domain-btn">Remove</button>
                                    <input type="hidden" name="allowed_domains[]" value="${domain}">`;
                    domainsList.appendChild(li);
                    domainInput.value = '';
                }
            });
        
            domainsList.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-domain-btn')) {
                    e.target.parentElement.remove();
                }
            });
        });
        </script>      
</body>
</html>
@endsection
