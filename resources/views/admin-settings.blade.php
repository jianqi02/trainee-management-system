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

        .expertises-list li,
        .departments-list li,
        .sections-list li {
            background-color: #f9f9f9;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .expertises-list li span,
        .departments-list li span,
        .sections-list li span {
            flex-grow: 1;
        }

        .expertises-list li .remove-expertise-btn,
        .departments-list li .remove-department-btn,
        .sections-list li .remove-section-btn {
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
                <label for="email-domain">Required Email Domain
                    <button type="button" id="toggle-email-domains" style="font-size: 12px; margin-left: 10px;">Show/Hide</button>
                </label>
                <div id="email-domains-container" style="display: none;">
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

            <!-- Expertise -->
            <div class="setting-item">
                <label for="expertises">
                    <h6>Expertise 
                        <button type="button" id="toggle-expertises" style="font-size: 12px; margin-left: 10px;">Show/Hide</button>
                    </h6>
                </label>
                
                <!-- Collapsible section -->
                <div id="expertises-container" style="display: none;">
                    <small>Expertises can be used when editing trainee's profile and will be used at supervisor assignment recommendation.</small>
                    <ul id="expertises-list" class="expertises-list">
                        @if($expertises->isEmpty())
                            <li>No expertises added yet.</li>
                        @else
                            @foreach($expertises as $expertise)
                                <li>
                                    <span>{{ $expertise }}</span>
                                    <button type="button" class="remove-expertise-btn">Remove</button>
                                    <input type="hidden" name="expertises[]" value="{{ $expertise }}">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <input type="text" id="expertise-input" placeholder="Add new expertise">
                    <button type="button" class="add-expertise-btn">Add Expertise</button>
                </div>
            </div>

            <!-- Department -->
            <div class="setting-item">
                <label for="department">
                    <h6>Department
                        <button type="button" id="toggle-departments" style="font-size: 12px; margin-left: 10px;">Show/Hide</button>
                    </h6>
                </label>
                
                <!-- Collapsible section -->
                <div id="departments-container" style="display: none;">
                    <small>You can set up the departments of your company here.</small>
                    <ul id="departments-list" class="departments-list">
                        @if($departments->isEmpty())
                            <li>No departments added yet.</li>
                        @else
                            @foreach($departments as $department)
                                <li>
                                    <span>{{ $department }}</span>
                                    <button type="button" class="remove-department-btn">Remove</button>
                                    <input type="hidden" name="departments[]" value="{{ $department }}">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <input type="text" id="department-input" placeholder="Add new department">
                    <button type="button" class="add-department-btn">Add Department</button>
                </div>
            </div>

            <!-- Section -->
            <div class="setting-item">
                <label for="section">
                    <h6>Section
                        <button type="button" id="toggle-sections" style="font-size: 12px; margin-left: 10px;">Show/Hide</button>
                    </h6>
                </label>
                
                <!-- Collapsible section -->
                <div id="sections-container" style="display: none;">
                    <small>You can set up the sections of your company here.</small>
                    <ul id="sections-list" class="sections-list">
                        @if($sections->isEmpty())
                            <li>No sections added yet.</li>
                        @else
                            @foreach($sections as $section)
                                <li>
                                    <span>{{ $section }}</span>
                                    <button type="button" class="remove-section-btn">Remove</button>
                                    <input type="hidden" name="sections[]" value="{{ $section }}">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <input type="text" id="section-input" placeholder="Add new section">
                    <button type="button" class="add-section-btn">Add Section</button>
                </div>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const expertiseInput = document.getElementById('expertise-input');
                const addExpertiseBtn = document.querySelector('.add-expertise-btn');
                const expertisesList = document.getElementById('expertises-list');
            
                addExpertiseBtn.addEventListener('click', function () {
                    const expertise = expertiseInput.value.trim();
            
                    if (expertise) {
                        const li = document.createElement('li');
                        li.innerHTML = `<span>${expertise}</span> 
                                        <button type="button" class="remove-expertise-btn">Remove</button>
                                        <input type="hidden" name="expertises[]" value="${expertise}">`;
                        expertisesList.appendChild(li);
                        expertiseInput.value = '';
                    }
                });
            
                expertisesList.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-expertise-btn')) {
                        e.target.parentElement.remove();
                    }
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const departmentInput = document.getElementById('department-input');
                const addDepartmentBtn = document.querySelector('.add-department-btn');
                const departmentsList = document.getElementById('departments-list');
            
                addDepartmentBtn.addEventListener('click', function () {
                    const department = departmentInput.value.trim();
            
                    if (department) {
                        const li = document.createElement('li');
                        li.innerHTML = `<span>${department}</span> 
                                        <button type="button" class="remove-department-btn">Remove</button>
                                        <input type="hidden" name="departments[]" value="${department}">`;
                        departmentsList.appendChild(li);
                        departmentInput.value = '';
                    }
                });
            
                departmentsList.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-department-btn')) {
                        e.target.parentElement.remove();
                    }
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sectionInput = document.getElementById('section-input');
                const addSectionBtn = document.querySelector('.add-section-btn');
                const sectionsList = document.getElementById('sections-list');
            
                addSectionBtn.addEventListener('click', function () {
                    const section = sectionInput.value.trim();
            
                    if (section) {
                        const li = document.createElement('li');
                        li.innerHTML = `<span>${section}</span> 
                                        <button type="button" class="remove-section-btn">Remove</button>
                                        <input type="hidden" name="sections[]" value="${section}">`;
                        sectionsList.appendChild(li);
                        sectionInput.value = '';
                    }
                });
            
                sectionsList.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-section-btn')) {
                        e.target.parentElement.remove();
                    }
                });
            });
        </script>
        <script>
            document.getElementById('toggle-expertises').addEventListener('click', function () {
                var container = document.getElementById('expertises-container');
                if (container.style.display === 'none' || container.style.display === '') {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });

            document.getElementById('toggle-email-domains').addEventListener('click', function () {
                var container = document.getElementById('email-domains-container');
                if (container.style.display === 'none' || container.style.display === '') {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });

            document.getElementById('toggle-departments').addEventListener('click', function () {
                var container = document.getElementById('departments-container');
                if (container.style.display === 'none' || container.style.display === '') {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });

            document.getElementById('toggle-sections').addEventListener('click', function () {
                var container = document.getElementById('sections-container');
                if (container.style.display === 'none' || container.style.display === '') {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });
        </script>
</body>
</html>
@endsection
