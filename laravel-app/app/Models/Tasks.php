<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $table = 'user_tasks';
    protected $fillable = ['name', 'deadlin', 'status', 'description', 'created_by'];

    public function users()
    {
        // The 'task_user' table is used as the pivot table in this relationship.
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }

    // Defines a many-to-many relationship between tasks and projects.
    public function projects()
    {
        // The 'project_task' table is used as the pivot table in this relationship.
        return $this->belongsToMany(Project::class, 'project_task', 'task_id', 'project_id');
    }

    use HasFactory;

}