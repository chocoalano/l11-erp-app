<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nik')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('placebirth', 20)->nullable();
            $table->date('datebirth')->nullable();
            $table->enum('gender', ['m', 'w'])->nullable();
            $table->enum('blood', ['a', 'b', 'o', 'ab'])->nullable();
            $table->enum('marital_status', ['single', 'marriade', 'widow', 'widower',])->nullable();
            $table->enum('religion', ['islam','protestant','catholic','hindu','buddha','khonghucu'])->nullable();
            $table->string('image', 100)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('u_addres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('idtype', ['ktp','passport'])->default('ktp');
            $table->string('idnumber', 100)->nullable();
            $table->date('idexpired')->nullable();
            $table->boolean('ispermanent')->default(false)->nullable();
            $table->string('postalcode', 10)->nullable();
            $table->text('citizen_id_address')->nullable();
            $table->boolean('use_as_residential')->default(false)->nullable();
            $table->text('residential_address')->nullable();
            $table->timestamps();
        });

        Schema::create('u_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->string('bank_account_holder', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('u_bpjs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('bpjs_ketenagakerjaan', 100)->nullable();
            $table->string('npp_bpjs_ketenagakerjaan', 100)->nullable();
            $table->date('bpjs_ketenagakerjaan_date')->nullable();
            $table->string('bpjs_kesehatan', 100)->nullable();
            $table->string('bpjs_kesehatan_family', 100)->nullable();
            $table->date('bpjs_kesehatan_date')->nullable();
            $table->float('bpjs_kesehatan_cost', 20)->default(0);
            $table->date('jht_cost')->nullable();
            $table->string('jaminan_pensiun_cost', 100)->nullable();
            $table->date('jaminan_pensiun_date')->nullable();
            $table->timestamps();
        });

        Schema::create('u_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name', 100)->nullable();
            $table->string('relationship', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('profesion', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('job_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->text('full_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->text('full_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('u_employes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('organization_id')->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->unsignedBigInteger('job_position_id')->foreign('job_position_id')->references('id')->on('job_positions')->onDelete('cascade');
            $table->unsignedBigInteger('job_level_id')->foreign('job_level_id')->references('id')->on('job_levels')->onDelete('cascade');
            $table->unsignedBigInteger('approval_line')->foreign('approval_line')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('approval_manager')->foreign('approval_manager')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('company_id')->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('branch_id')->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->enum('status', ['contract','permanent','magang','last daily'])->nullable();
            $table->date('join_date')->nullable();
            $table->date('sign_date')->nullable();
            $table->timestamps();
        });

        Schema::create('u_families', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('fullname', 100)->nullable();
            $table->enum('relationship', ['wife','husband','mother','father','brother','sister','child'])->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('marital_status', ['single', 'marriade', 'widow', 'widower',])->nullable();
            $table->string('job')->nullable();
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('u_formal_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('grade_id')->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            $table->string('institution',100)->nullable();
            $table->string('majors',100)->nullable();
            $table->float('score')->nullable();
            $table->date('start')->nullable();
            $table->date('finish')->nullable();
            $table->string('description')->nullable();
            $table->boolean('certification')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('u_informal_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name', 100)->nullable();
            $table->date('start')->nullable();
            $table->date('finish')->nullable();
            $table->date('expired')->nullable();
            $table->enum('type', ['day', 'month', 'year'])->nullable();
            $table->integer('duration')->nullable();
            $table->float('fee')->nullable();
            $table->text('description')->nullable();
            $table->boolean('certification')->nullable();
            $table->timestamps();
        });

        Schema::create('u_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('basic_salary', 20)->default(0)->nullable();
            $table->enum('salary_type', ['Monthly', 'Weakly', 'Dayly'])->nullable();
            $table->string('payment_schedule', 100)->nullable();
            $table->string('prorate_settings', 100)->nullable();
            $table->string('overtime_settings', 100)->nullable();
            $table->string('cost_center', 100)->nullable();
            $table->string('cost_center_category', 100)->nullable();
            $table->string('currency', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('u_tax_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('npwp_15_digit_old', 20)->nullable();
            $table->string('npwp_16_digit_new', 20)->nullable();
            $table->enum('ptkp_status', ['TK0','TK1','TK2','TK3','K0','K1','K2','K3','K/I/0','K/I/1','K/I/2','K/I/3'])->nullable();
            $table->enum('tax_method', ['gross'] )->nullable();
            $table->enum('tax_salary', ['taxable'] )->nullable();
            $table->enum('emp_tax_status', ['permanent', 'contract', 'last-daily'] )->nullable();
            $table->float('beginning_netto', 20)->default(0)->nullable();
            $table->float('pph21_paid', 20)->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('u_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('company', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->text('length_of_service')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('u_addres');
        Schema::dropIfExists('u_banks');
        Schema::dropIfExists('u_bpjs');
        Schema::dropIfExists('u_emergency_contacts');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('job_positions');
        Schema::dropIfExists('job_levels');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('u_employes');
        Schema::dropIfExists('u_families');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('u_formal_education');
        Schema::dropIfExists('u_informal_education');
        Schema::dropIfExists('u_salaries');
        Schema::dropIfExists('u_tax_configs');
        Schema::dropIfExists('u_work_experiences');
    }
};
