<div class="container">

    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
      <div class="clearfix">
        <h1 class="app-page-title">Dashboard</h1>
        <span>Mon, Aug 01, 2024 - Sep 01, 2024 </span>
      </div>
      <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="fi fi-rr-plus me-1"></i> Add Employee
      </button>
    </div>

    <div class="row">

      <div class="col-xxl-9">

        <div class="row">
          <div class="col-6 col-md-4 col-lg">
            <div class="card bg-secondary bg-opacity-05 shadow-none border-0">
              <div class="card-body">
                <div class="avatar bg-secondary shadow-secondary rounded-circle text-white mb-3">
                  <i class="fi fi-sr-users"></i>
                </div>
                <h3>1206</h3>
                <h6 class="mb-0">Total Employee</h6>
                <small class="fw-medium">
                  <span class="text-success">
                    <i class="fi fi-rr-arrow-small-up scale-3x"></i> +5%
                  </span> Last Month
                </small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-lg">
            <div class="card bg-info bg-opacity-05 shadow-none border-0">
              <div class="card-body">
                <div class="avatar bg-info shadow-info rounded-circle text-white mb-3">
                  <i class="fi fi-sr-user-add"></i>
                </div>
                <h3>218</h3>
                <h6 class="mb-0">New Employee</h6>
                <small class="fw-medium">
                  <span class="text-success">
                    <i class="fi fi-rr-arrow-small-up scale-3x"></i> +3.2%
                  </span> Last Month
                </small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-lg">
            <div class="card bg-secondary bg-opacity-05 shadow-none border-0">
              <div class="card-body">
                <div class="avatar bg-warning shadow-warning rounded-circle text-white mb-3">
                  <i class="fi fi-sr-delete-user"></i>
                </div>
                <h3>126</h3>
                <h6 class="mb-0">On Leave</h6>
                <small class="fw-medium">
                  <span class="text-danger">
                    <i class="fi fi-rr-arrow-small-down scale-3x"></i> -2%
                  </span> Last Month
                </small>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-6 col-lg">
            <div class="card bg-success bg-opacity-05 shadow-none border-0">
              <div class="card-body">
                <div class="avatar bg-success shadow-success rounded-circle text-white mb-3">
                  <i class="fi fi-sr-shopping-bag"></i>
                </div>
                <h3>776</h3>
                <h6 class="mb-0">Job Applicants</h6>
                <small class="fw-medium">
                  <span class="text-success">
                    <i class="fi fi-rr-arrow-small-down scale-3x"></i> +8%
                  </span> Last Month
                </small>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg">
            <div class="card bg-danger bg-opacity-05 shadow-none border-0">
              <div class="card-body">
                <div class="avatar bg-danger shadow-danger rounded-circle text-white mb-3">
                  <i class="fi fi-sr-clock-three"></i>
                </div>
                <h3>1017</h3>
                <h6 class="mb-0">Over Time</h6>
                <small class="fw-medium">
                  <span class="text-danger">
                    <i class="fi fi-rr-arrow-small-down scale-3x"></i> -8%
                  </span> Last Month
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xxl-3">
        <div class="card overflow-hidden z-1">
          <div class="card-body">
            <div class="w-75">
              <h6 class="card-title">Create Announcement</h6>
              <p>Make a announcement to your employee</p>
            </div>
            <img src="{{ asset('template/assets/images/media/svg/media1.svg') }}" alt="" class="position-absolute bottom-0 end-0 z-n1">
          </div>
          <div class="card-footer border-0 pt-0">
            <a href="#" class="btn btn-outline-light waves-effect btn-shadow">Create Now</a>
          </div>
        </div>
      </div>

      <div class="col-xxl-7 col-lg-6">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
            <h6 class="card-title mb-0">Employee Structure</h6>
            <button type="button" class="btn btn-sm btn-outline-light btn-shadow waves-effect">Download Report</button>
          </div>
          <div class="card-body p-2">
            <div id="chartEmployee"></div>
          </div>
        </div>
      </div>

      <div class="col-xxl-5 col-lg-6">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
            <h6 class="card-title mb-0">Company Pay</h6>
            <select class="selectpicker" data-style="btn-sm btn-outline-light btn-shadow waves-effect">
              <option value="pending">2024</option>
              <option>2023</option>
              <option>2022</option>
              <option>2021</option>
            </select>
          </div>
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-sm-6">
                <div class="maxw-250px ratio ratio-1x1">
                  <canvas id="companyPayChart"></canvas>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="d-grid gap-2">
                  <div class="d-flex gap-1 align-items-center mx-1">
                    <i class="fa fa-circle text-danger text-2xs me-1"></i>
                    <strong class="text-dark fw-semibold">15%</strong> Salary
                  </div>
                  <div class="d-flex gap-1 align-items-center mx-1">
                    <i class="fa fa-circle text-success text-2xs me-1"></i>
                    <strong class="text-dark fw-semibold">08%</strong> Bonus
                  </div>
                  <div class="d-flex gap-1 align-items-center mx-1">
                    <i class="fa fa-circle text-info text-2xs me-1"></i>
                    <strong class="text-dark fw-semibold">20%</strong> Commission
                  </div>
                  <div class="d-flex gap-1 align-items-center mx-1">
                    <i class="fa fa-circle text-secondary text-2xs me-1"></i>
                    <strong class="text-dark fw-semibold">11%</strong> Overtime
                  </div>
                  <div class="d-flex gap-1 align-items-center mx-1">
                    <i class="fa fa-circle text-primary text-2xs me-1"></i>
                    <strong class="text-dark fw-semibold">28%</strong> Reimbursement
                  </div>
                  <div class="d-flex gap-1 align-items-center mx-1">
                    <i class="fa fa-circle text-warning text-2xs me-1"></i>
                    <strong class="text-dark fw-semibold">18%</strong> Benefits
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="row gy-3 align-items-center">
              <div class="col-sm-6">
                <p class="mb-0">2024 Download Report Company Trends and Insights</p>
              </div>
              <div class="col-sm-6 text-sm-end">
                <button type="button" class="btn btn-primary waves-effect waves-light">
                  <i class="fi fi-rr-download me-1"></i> Download Report
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header py-3">
            <h5 class="modal-title">Add Employee</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullName" placeholder="Enter full name">
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="email" placeholder="example@email.com">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" placeholder="+91 9876543210">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="department" class="form-label">Department</label>
                  <select class="form-select" id="department">
                    <option selected disabled>Select Department</option>
                    <option>HR</option>
                    <option>Development</option>
                    <option>Sales</option>
                    <option>Marketing</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="designation" class="form-label">Designation</label>
                  <input type="text" class="form-control" id="designation" placeholder="e.g. Software Engineer">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="joiningDate" class="form-label">Joining Date</label>
                  <input type="date" class="form-control flatpickr-date" id="joiningDate">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="status" class="form-label">Employment Status</label>
                  <select class="form-select" id="status">
                    <option>Active</option>
                    <option>Inactive</option>
                    <option>Probation</option>
                  </select>
                </div>
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" rows="2" placeholder="Enter address"></textarea>
              </div>
              <div class="mb-3">
                <label for="photo" class="form-label">Profile Photo</label>
                <input class="form-control" type="file" id="photo">
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-success">Add Employee</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

</div>
