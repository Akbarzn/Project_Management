# Folder Structure Cleanup Tasks

## 1. Fix Request Namespaces
- [ ] Update app/Http/Requests/Client/ProjectRequest/StoreProjectRequest.php namespace to App\Http\Requests\Client\ProjectRequest
- [ ] Update app/Http/Requests/Client/ProjectRequest/UpdateProjectRequest.php namespace to App\Http\Requests\Client\ProjectRequest
- [ ] Update app/Http/Requests/Manager/Project/StoreProjectRequest.php namespace to App\Http\Requests\Manager\Project
- [ ] Update app/Http/Requests/Manager/Project/UpdateProjectRequest.php namespace to App\Http\Requests\Manager\Project

## 2. Rename View Folders
- [ ] Rename resources/views/manager/ to resources/views/managers/
- [ ] Rename resources/views/clients/project_request/ to resources/views/clients/project-requests/

## 3. Update Controller View Paths
- [ ] Update ProjectRequestController view paths from 'client.project_request.*' to 'clients.project-requests.*'
- [ ] Update ProjectController view paths from 'manager.projects.*' to 'managers.projects.*'
- [ ] Update other controllers' view paths accordingly

## 4. Update Use Statements in Controllers
- [ ] Update use statements in controllers to match new request namespaces
