<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tasks;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'created_by',
    ];


    // Defines a many-to-many relationship with the Tasks model.
    public function tasks()
    {
        // Specifies 'project_task' as the pivot table and customizes the foreign key names.
        return $this->belongsToMany(Tasks::class, 'project_task', 'project_id', 'task_id');
    }

    use HasFactory;
}

//                     'project_task' pivot table 
// +----------------+       +--------------+       +-------------+
// |   projects     |       | project_task |       |  user_tasks |
// +----------------+       +--------------+       +-------------+
// | id (PK)        |1    * | id           |*    1 | id (PK)     |
// | title          |-------| project_id   |-------| name        |
// | description    |       | task_id      |       | assignTo    |
// | created_at     |       | created_at   |       | deadline    |
// | updated_at     |       | updated_at   |       | status      |
// +----------------+       +--------------+       +-------------+