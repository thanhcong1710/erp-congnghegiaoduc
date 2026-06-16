import { GoogleGenerativeAI, SchemaType } from "@google/generative-ai";
import dotenv from "dotenv";

// Load biến môi trường
dotenv.config();
const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// ==========================================
// ĐỊNH NGHĨA KỸ NĂNG (SKILL)
// ==========================================
function getStudentInfo(studentId) {
    const erpDatabase = {
        "SV001": { name: "Nguyễn Văn A", class: "10A1", status: "Đang học", tuition_paid: true },
        "SV002": { name: "Trần Thị B", class: "11B2", status: "Bảo lưu", tuition_paid: false },
    };
    return erpDatabase[studentId] || { error: "Không tìm thấy học sinh" };
}

const getStudentInfoTool = {
    functionDeclarations: [{
        name: "getStudentInfo",
        description: "Truy xuất thông tin của học sinh từ hệ thống ERP dựa vào mã học sinh.",
        parameters: {
            type: SchemaType.OBJECT,
            properties: { 
                studentId: { type: SchemaType.STRING, description: "Mã học sinh" } 
            },
            required: ["studentId"],
        },
    }],
};

// ==========================================
// KHỞI TẠO 2 MODEL ĐỂ SO SÁNH
// ==========================================
// 1. Model "Chay" (Chỉ là LLM bình thường, không có khả năng tương tác với hệ thống)
const modelWithoutSkill = genAI.getGenerativeModel({ model: "gemini-flash-latest" });

// 2. Model "Agent" (Được trang bị thêm Skill)
const modelWithSkill = genAI.getGenerativeModel({ 
    model: "gemini-flash-latest",
    tools: [getStudentInfoTool] 
});

// ==========================================
// CHẠY THỬ NGHIỆM SO SÁNH
// ==========================================
async function runComparison() {
    const prompt = "Kiểm tra giúp tôi học sinh mã SV001 đã đóng học phí chưa?";
    console.log("===============================================================");
    console.log(`👤 CÂU HỎI CỦA BẠN: "${prompt}"`);
    console.log("===============================================================\n");

    // ---------------------------------------------------------
    // TRƯỜNG HỢP 1: KHÔNG DÙNG SKILL
    // ---------------------------------------------------------
    console.log("🔴 TRƯỜNG HỢP 1: CHATBOT BÌNH THƯỜNG (KHÔNG CÓ SKILL)");
    console.log("   -> Model chỉ dựa vào kiến thức có sẵn từ lúc được huấn luyện...");
    try {
        const result1 = await modelWithoutSkill.generateContent(prompt);
        console.log(`\n🤖 TRẢ LỜI: "${result1.response.text().trim()}"`);
    } catch (e) {
        console.log("Lỗi:", e.message);
    }
    console.log("\n---------------------------------------------------------------\n");

    // ---------------------------------------------------------
    // TRƯỜNG HỢP 2: CÓ DÙNG SKILL
    // ---------------------------------------------------------
    console.log("🟢 TRƯỜNG HỢP 2: AGENT THÔNG MINH (CÓ SKILL getStudentInfo)");
    console.log("   -> Agent có thể tự động tìm kiếm và lấy dữ liệu thật từ ERP...");
    try {
        const chat = modelWithSkill.startChat();
        const result2 = await chat.sendMessage(prompt);
        const call = result2.response.functionCalls();

        if (call && call.length > 0) {
            const functionCall = call[0];
            console.log(`\n   [🧠 Tư duy] Agent nhận ra cần dùng skill: ${functionCall.name} với tham số: ${JSON.stringify(functionCall.args)}`);
            
            // Code thực thi việc gọi vào Database/API
            console.log(`   [⚙️ Hành động] Đang chạy code truy vấn Database...`);
            const data = getStudentInfo(functionCall.args.studentId);
            console.log(`   [📦 Kết quả] Lấy được dữ liệu:`, data);

            // Gửi dữ liệu về lại cho Agent để nó dịch ra tiếng người
            const finalResult = await chat.sendMessage([{
                functionResponse: { name: functionCall.name, response: data }
            }]);
            console.log(`\n🤖 TRẢ LỜI: "${finalResult.response.text().trim()}"`);
        } else {
            console.log(`\n🤖 TRẢ LỜI: "${result2.response.text()}"`);
        }
    } catch (e) {
         console.log("Lỗi:", e.message);
    }
    console.log("\n===============================================================\n");
}

runComparison();
