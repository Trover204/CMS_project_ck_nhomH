<?php
/**
 * Frontend Vietnamese Translation for Nhom H Website
 * Only applied to frontend, keeping wp-admin completely in original English.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', function() {
    // Only apply on frontend requests, never in admin or ajax admin
    if ( is_admin() && ! wp_doing_ajax() ) {
        return;
    }

    add_filter( 'gettext', 'nhomh_translate_frontend_strings', 99, 3 );
    add_filter( 'gettext_with_context', 'nhomh_translate_frontend_strings_context', 99, 4 );
} );

/**
 * Main translation mapping for frontend
 */
function nhomh_get_translation_dictionary() {
    static $dict = null;
    if ( null !== $dict ) {
        return $dict;
    }

    $dict = array(
        // Header & Navigation
        'Home'                      => 'Trang Chủ',
        'Jobs'                      => 'Việc Làm',
        'Job Dashboard'             => 'Bảng Điều Khiển',
        'Post a Job'                => 'Đăng Tin Tuyển Dụng',
        'Post Jobs'                 => 'Đăng Tin Tuyển Dụng',
        'NEWS'                      => 'Tin Tức',
        'About'                     => 'Giới Thiệu',
        'Contact'                   => 'Liên Hệ',
        'Mobile'                    => 'Menu',
        'Skip to content (Press Enter)' => 'Chuyển đến nội dung chính',

        // Hero Banner & Search Form
        'FIND YOUR DREAM JOBS'      => 'TÌM CÔNG VIỆC MƠ ƯỚC',
        'Aim Higher, Dream Bigger'  => 'Vươn Xa Hơn, Ước Mơ Lớn Hơn',
        'Keywords'                  => 'Từ khóa',
        'Search for jobs, companies, skills' => 'Tìm kiếm việc làm, công ty, kỹ năng...',
        'SEARCH JOB'                => 'TÌM VIỆC LÀM',
        'Search'                    => 'Tìm Kiếm',
        'Location'                  => 'Khu vực',
        'Khu vực'                   => 'Khu vực',

        // Homepage Sections
        'Job Posting'               => 'Tin Tuyển Dụng Mới Nhất',
        'TOP JOBS'                  => 'VIỆC LÀM HÀNG ĐẦU',
        'VIEW MORE JOBS'            => 'XEM THÊM VIỆC LÀM',
        'VIEW ALL JOBS'             => 'XEM TẤT CẢ VIỆC LÀM',
        'Created: %s'               => 'Ngày đăng: %s',
        'CAREER WITH US'            => 'CƠ HỘI NGHỀ NGHIỆP',
        'MORE ABOUT US'             => 'TÌM HIỂU THÊM VỀ CHÚNG TÔI',
        'Latest Articles'           => 'Bài Viết Mới Nhất',
        'See More Posts'            => 'Xem Thêm Bài Viết',
        'Clients Testimonials'      => 'Ý Kiến Khách Hàng / Đối Tác',

        // Jobs Page
        'ALL JOBS'                  => 'TẤT CẢ VIỆC LÀM',
        'Sort Jobs'                 => 'Sắp xếp việc làm',
        'Latest Jobs'               => 'Mới nhất',
        'Oldest Jobs'               => 'Cũ nhất',
        'Title A-Z'                 => 'Tiêu đề A-Z',
        'Fulltime'                  => 'Toàn thời gian',
        'Part-time'                 => 'Bán thời gian',
        'Temporary'                 => 'Tạm thời',
        'Freelance'                 => 'Tự do',
        'Internship'                => 'Thực tập',
        'Category Name'             => 'Ngành nghề',

        // Job Detail Page
        'SHARE'                     => 'CHIA SẺ',
        'APPLY JOB'                 => 'ỨNG TUYỂN NGAY',
        'Overview about Company'    => 'Tổng Quan Về Doanh Nghiệp',
        'Our Key Skills'            => 'Kỹ Năng & Yêu Cầu Chính',
        'Why You\'ll Love Working Here' => 'Lý Do Bạn Sẽ Thích Làm Việc Tại Đây',
        'Staff Rating'              => 'Đánh Giá Của Nhân Viên',
        'Company Photos'            => 'Hình Ảnh Hoạt Động',
        'Click to view company photos' => 'Bấm để xem hình ảnh',
        'Apply for job'             => 'Ứng tuyển công việc',

        // About Us Page
        'ABOUT US'                  => 'VỀ CHÚNG TÔI',
        'SHARE “OMOTENASHI”'        => 'CHIA SẺ TINH THẦN “OMOTENASHI”',
        'WITH THE WORLD'            => 'ĐẾN TOÀN THẾ GIỚI',
        'Our Vision'                => 'Tầm Nhìn',
        'Ours Mission'              => 'Sứ Mệnh',
        'Our Core Value'            => 'Giá Trị Cốt Lõi',
        'Hotels, restaurants, banquets/weddings management' => 'Quản lý khách sạn, nhà hàng và sự kiện - tiệc cưới',
        'Established since'         => 'Năm thành lập',
        'Head Office'               => 'Trụ sở chính',
        'Capital'                   => 'Vốn điều lệ',
        'CEO'                       => 'Tổng Giám Đốc',
        'Number of Employees'       => 'Quy mô nhân sự',

        // Contact Page
        'CONTACT US'                => 'LIÊN HỆ VỚI CHÚNG TÔI',
        'Our Headquarters Address'  => 'Địa Chỉ Trụ Sở Chính',
        'For Employers'             => 'Dành Cho Nhà Tuyển Dụng',
        'Call our Sales Hotline'    => 'Hotline tư vấn tuyển dụng',
        'For Jobseekers'            => 'Dành Cho Ứng Viên',
        'Call our Customer Care'    => 'Tổng đài hỗ trợ ứng viên',
        'Ho Chi Minh'               => 'Hồ Chí Minh',
        'Ha Noi'                    => 'Hà Nội',
        'Da Nang'                   => 'Đà Nẵng',

        // News Page
        'PDS NEWS'                  => 'TIN TỨC & BÀI VIẾT',
        'NEWEST BLOG ENTRIES'       => 'BÀI VIẾT MỚI NHẤT',
        'Older Posts'               => 'Bài viết cũ hơn',
        'Newer Posts'               => 'Bài viết mới hơn',
        'Read More'                 => 'Xem chi tiết',

        // Footer & Newsletter
        'Subscribe To'              => 'Đăng Ký',
        'Our Newsletter'            => 'Nhận Bản Tin',
        'Input your email address'  => 'Nhập địa chỉ email của bạn',
        'SUBSCRIBE'                 => 'ĐĂNG KÝ',
        'JOBS'                      => 'VIỆC LÀM',
        'COMPANIES'                 => 'CÔNG TY',
        'BLOG'                      => 'TIN TỨC',
        'ABOUT'                     => 'GIỚI THIỆU',
        'CONTACT'                   => 'LIÊN HỆ',
        'All Rights Reserved'       => 'Bảo lưu mọi quyền',
    );

    return $dict;
}

function nhomh_translate_frontend_strings( $translation, $text, $domain ) {
    if ( is_admin() && ! wp_doing_ajax() ) {
        return $translation;
    }

    $dict = nhomh_get_translation_dictionary();
    $clean_text = trim( $text );

    if ( isset( $dict[ $clean_text ] ) ) {
        return $dict[ $clean_text ];
    }

    return $translation;
}

function nhomh_translate_frontend_strings_context( $translation, $text, $context, $domain ) {
    return nhomh_translate_frontend_strings( $translation, $text, $domain );
}
