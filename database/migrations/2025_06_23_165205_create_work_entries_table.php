<?php
// database/migrations/xxxx_xx_xx_create_work_entries_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('work_entries', function (Blueprint $table) {
            $table->id();
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('task_description');
            $table->decimal('duration', 8, 2); // hours with 2 decimal places
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('work_date');
            $table->index(['work_date', 'start_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_entries');
    } 
}