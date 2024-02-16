<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained('user_tasks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task');
    }
};


// +----------------+       +--------------+       +-------------+
// |   projects     |       | project_task |       |  user_tasks |
// +----------------+       +--------------+       +-------------+
// | id (PK)        |1    * | id           |*    1 | id (PK)     |
// | title          |-------| project_id   |-------| name        |
// | description    |       | task_id      |       | assignTo    |
// | created_at     |       | created_at   |       | deadline    |
// | updated_at     |       | updated_at   |       | status      |
// +----------------+       +--------------+       +-------------+